<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceAnalyticsController extends Controller
{
    /**
     * Display attendance analytics dashboard
     */
    public function index(Request $request)
    {
        // Get current semester
        $currentSemester = Semester::where('is_current', true)->first();
        $selectedSemester = $request->input('semester_id', $currentSemester ? $currentSemester->id : null);

        // Get filters
        $departmentId = $request->input('department_id');
        $courseId = $request->input('course_id');
        $year = $request->input('year');
        $dateRange = $request->input('date_range', 'this_month');

        // Build date range
        $dateFrom = null;
        $dateTo = null;
        switch ($dateRange) {
            case 'today':
                $dateFrom = Carbon::today();
                $dateTo = Carbon::today();
                break;
            case 'this_week':
                $dateFrom = Carbon::now()->startOfWeek();
                $dateTo = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $dateFrom = Carbon::now()->startOfMonth();
                $dateTo = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $dateFrom = Carbon::now()->subMonth()->startOfMonth();
                $dateTo = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_semester':
                if ($currentSemester && $currentSemester->start_date) {
                    $dateFrom = $currentSemester->start_date;
                    $dateTo = $currentSemester->end_date ?? Carbon::now();
                }
                break;
        }

        // Get statistics
        $totalSessions = AttendanceSession::count();
        $totalRecords = AttendanceRecord::count();
        $presentCount = AttendanceRecord::where('status', 'present')->count();
        $lateCount = AttendanceRecord::where('status', 'late')->count();
        $absentCount = AttendanceRecord::where('status', 'absent')->count();

        // Calculate average attendance
        $totalStudents = User::where('role_id', 3)->count();
        $totalPossible = $totalStudents * $totalSessions;
        $avgAttendance = $totalPossible > 0 ? round((($presentCount + $lateCount) / $totalPossible) * 100, 1) : 0;

        // Get department attendance
        $departmentAttendance = $this->getDepartmentAttendance($departmentId, $dateFrom, $dateTo);

        // Weekly trend
        $weeklyTrend = $this->getWeeklyTrend($departmentId, $courseId, $year, $dateFrom, $dateTo);

        // Course ranking
        $courseRanking = $this->getCourseRanking($departmentId, $dateFrom, $dateTo);

        // At-risk students
        $atRiskStudents = $this->getAtRiskStudents($departmentId, $year);

        // Eligibility stats
        $eligibilityStats = $this->getEligibilityStats($departmentId);

        // Stats array for view
        $stats = [
            'total_sessions' => $totalSessions,
            'total_records' => $totalRecords,
            'present_count' => $presentCount,
            'late_count' => $lateCount,
            'absent_count' => $absentCount,
            'avg_attendance' => $avgAttendance,
        ];

        // Year labels for filter
        $yearLabels = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];

        // Get filter options
        $departments = Department::orderBy('name')->get();
        $courses = Course::where('is_active', true)->orderBy('course_code')->get();

        // ✅ FIXED: Use correct column names for semesters
        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_number', 'asc')
            ->get();

        return view('admin.attendance.analytics', compact(
            'stats',
            'departmentAttendance',
            'weeklyTrend',
            'courseRanking',
            'atRiskStudents',
            'eligibilityStats',
            'departments',
            'courses',
            'semesters',
            'selectedSemester',
            'departmentId',
            'courseId',
            'year',
            'dateRange',
            'dateFrom',
            'dateTo',
            'yearLabels'
        ));
    }

    /**
     * Display all attendance records across the university
     */
    public function records(Request $request)
    {
        $query = AttendanceRecord::with(['session.course', 'student']);

        // Filter by department
        if ($request->filled('department_id')) {
            $query->whereHas('session.course', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('session', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = Department::orderBy('name')->get();
        $courses = Course::where('is_active', true)->orderBy('course_code')->get();

        return view('admin.attendance.records', compact('records', 'departments', 'courses'));
    }

    /**
     * Get department attendance data
     */
    private function getDepartmentAttendance($filterDepartmentId = null, $dateFrom = null, $dateTo = null)
    {
        $departments = Department::all();
        $result = [];

        foreach ($departments as $dept) {
            if ($filterDepartmentId && $dept->id != $filterDepartmentId) {
                continue;
            }

            $courseIds = Course::where('department_id', $dept->id)->pluck('id')->toArray();
            $totalStudents = User::where('role_id', 3)
                ->where('department_id', $dept->id)
                ->count();

            if (empty($courseIds) || $totalStudents == 0) {
                $result[] = [
                    'id' => $dept->id,
                    'name' => $dept->code,
                    'full_name' => $dept->name,
                    'attendance' => 0,
                    'total_students' => $totalStudents,
                    'total_courses' => 0,
                    'sessions' => 0,
                ];
                continue;
            }

            $sessionQuery = AttendanceSession::whereIn('course_id', $courseIds);
            if ($dateFrom) {
                $sessionQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $sessionQuery->whereDate('created_at', '<=', $dateTo);
            }
            $sessions = $sessionQuery->count();

            // Get session IDs
            $sessionIds = $sessionQuery->pluck('id')->toArray();

            $recordQuery = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)
                ->whereIn('status', ['present', 'late']);

            $records = $recordQuery->count();

            $expected = $sessions * $totalStudents;
            $attendance = $expected > 0 ? round(($records / $expected) * 100, 1) : 0;

            $result[] = [
                'id' => $dept->id,
                'name' => $dept->code,
                'full_name' => $dept->name,
                'attendance' => $attendance,
                'total_students' => $totalStudents,
                'total_courses' => count($courseIds),
                'sessions' => $sessions,
            ];
        }

        usort($result, function($a, $b) {
            return $b['attendance'] - $a['attendance'];
        });

        return $result;
    }

    /**
     * Get weekly attendance trend
     */
    private function getWeeklyTrend($departmentId = null, $courseId = null, $year = null, $dateFrom = null, $dateTo = null)
    {
        $weeks = 12;
        $trend = [];
        $startDate = $dateFrom ? Carbon::parse($dateFrom)->subWeeks($weeks) : Carbon::now()->subWeeks($weeks);

        // Get total students
        $totalStudents = User::where('role_id', 3)->count();
        if ($departmentId) {
            $totalStudents = User::where('role_id', 3)
                ->where('department_id', $departmentId)
                ->count();
        }

        // Get all session IDs for the period
        $sessionQuery = AttendanceSession::where('created_at', '>=', $startDate);
        if ($dateTo) {
            $sessionQuery->whereDate('created_at', '<=', $dateTo);
        }
        if ($departmentId) {
            $sessionQuery->whereHas('course', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }
        if ($courseId) {
            $sessionQuery->where('course_id', $courseId);
        }
        $sessionIds = $sessionQuery->pluck('id')->toArray();

        $recordQuery = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)
            ->whereIn('status', ['present', 'late']);

        if ($year) {
            $recordQuery->whereHas('student', function($q) use ($year) {
                $q->where('current_year', $year);
            });
        }

        $records = $recordQuery->get();

        for ($i = $weeks; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
            $label = $weekStart->format('d M');

            $weekSessionQuery = AttendanceSession::whereBetween('created_at', [$weekStart, $weekEnd]);
            if ($departmentId) {
                $weekSessionQuery->whereHas('course', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            if ($courseId) {
                $weekSessionQuery->where('course_id', $courseId);
            }
            $weekSessionIds = $weekSessionQuery->pluck('id')->toArray();

            $weekRecords = $records->filter(function($record) use ($weekSessionIds) {
                return in_array($record->attendance_session_id, $weekSessionIds);
            });

            $expected = $totalStudents * count($weekSessionIds);
            $attendance = $expected > 0 ? round(($weekRecords->count() / $expected) * 100, 1) : 0;

            $trend[] = [
                'label' => $label,
                'attendance' => $attendance,
                'records' => $weekRecords->count(),
                'expected' => $expected,
                'sessions' => count($weekSessionIds),
            ];
        }

        return $trend;
    }

    /**
     * Get course attendance ranking
     */
    private function getCourseRanking($departmentId = null, $dateFrom = null, $dateTo = null)
    {
        $query = Course::with(['department'])
            ->where('is_active', true);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $courses = $query->get();
        $ranking = [];

        foreach ($courses as $course) {
            $totalStudents = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count();

            $sessionQuery = AttendanceSession::where('course_id', $course->id);
            if ($dateFrom) {
                $sessionQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $sessionQuery->whereDate('created_at', '<=', $dateTo);
            }
            $sessions = $sessionQuery->count();
            $sessionIds = $sessionQuery->pluck('id')->toArray();

            $recordQuery = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)
                ->whereIn('status', ['present', 'late']);

            $records = $recordQuery->count();

            $expected = $sessions * $totalStudents;
            $attendance = $expected > 0 ? round(($records / $expected) * 100, 1) : 0;

            if ($sessions > 0) {
                $ranking[] = [
                    'course_id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_name' => $course->course_name,
                    'department' => $course->department->code ?? 'N/A',
                    'attendance' => $attendance,
                    'students' => $totalStudents,
                    'sessions' => $sessions,
                    'records' => $records,
                ];
            }
        }

        usort($ranking, function($a, $b) {
            return $b['attendance'] - $a['attendance'];
        });

        return array_slice($ranking, 0, 20);
    }

    /**
     * Get at-risk students based on attendance
     */
    private function getAtRiskStudents($departmentId = null, $year = null)
    {
        $query = User::where('role_id', 3)
            ->with(['department', 'enrollments.course']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($year) {
            $query->where('current_year', $year);
        }

        $students = $query->get();
        $result = [];

        foreach ($students as $student) {
            $enrollments = $student->enrollments()->where('status', 'approved')->get();
            $totalEnrollments = $enrollments->count();

            if ($totalEnrollments === 0) {
                continue;
            }

            $attendance = $enrollments->avg('attendance_percentage') ?? 0;
            $riskLevel = $attendance >= 75 ? 'Low' : ($attendance >= 60 ? 'Medium' : 'High');

            if ($riskLevel !== 'Low') {
                $result[] = [
                    'student' => $student,
                    'attendance' => round($attendance, 1),
                    'risk_level' => $riskLevel,
                    'enrollments' => $totalEnrollments,
                    'department' => $student->department->name ?? 'N/A',
                    'year' => $student->current_year ?? 'N/A',
                ];
            }
        }

        usort($result, function($a, $b) {
            $riskOrder = ['High' => 0, 'Medium' => 1, 'Low' => 2];
            return $riskOrder[$a['risk_level']] - $riskOrder[$b['risk_level']];
        });

        return $result;
    }

    /**
     * Get eligibility statistics
     */
    private function getEligibilityStats($departmentId = null)
    {
        $query = Enrollment::where('status', 'approved');

        if ($departmentId) {
            $query->whereHas('course', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $enrollments = $query->get();

        $eligible = $enrollments->filter(function($e) {
            return $e->eligibility_status === 'eligible';
        })->count();

        $warning = $enrollments->filter(function($e) {
            return $e->eligibility_status === 'warning';
        })->count();

        $notEligible = $enrollments->filter(function($e) {
            return $e->eligibility_status === 'not_eligible';
        })->count();

        $total = $enrollments->count();

        return [
            'eligible' => $eligible,
            'warning' => $warning,
            'not_eligible' => $notEligible,
            'total' => $total,
            'eligible_percentage' => $total > 0 ? round(($eligible / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get chart data for AJAX requests
     */
    public function chartData(Request $request)
    {
        $type = $request->input('type', 'weekly');
        $departmentId = $request->input('department_id');
        $courseId = $request->input('course_id');

        $data = [];

        switch ($type) {
            case 'weekly':
                $data = $this->getWeeklyTrend($departmentId, $courseId);
                break;
            case 'department':
                $data = $this->getDepartmentAttendance($departmentId);
                break;
            case 'course':
                $data = $this->getCourseRanking($departmentId);
                break;
        }

        return response()->json($data);
    }
}
