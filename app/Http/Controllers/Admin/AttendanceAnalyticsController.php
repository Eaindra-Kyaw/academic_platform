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
use App\Helpers\AttendanceHelper;
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
        $studentId = $request->input('student_id');
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

        // Weekly trend – if a student is selected, get that student's weekly attendance
        $weeklyTrend = $this->getWeeklyTrend($departmentId, $courseId, $year, $dateFrom, $dateTo, $studentId);

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

        // Students for dropdown
        $students = User::where('role_id', 3)->orderBy('name')->get(['id', 'name', 'student_id']);

        // Semesters
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
            'studentId',
            'students',
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
     * Get weekly attendance trend – supports student_id filter
     */
    private function getWeeklyTrend($departmentId = null, $courseId = null, $year = null, $dateFrom = null, $dateTo = null, $studentId = null)
    {
        $weeks = 12;
        $trend = [];

        // If a student is selected, compute trend from attendance records
        if ($studentId) {
            $startDate = $dateFrom ? Carbon::parse($dateFrom)->subWeeks($weeks) : Carbon::now()->subWeeks($weeks);

            // Get all session IDs that the student has records for
            $sessionIds = AttendanceRecord::where('student_id', $studentId)
                ->pluck('attendance_session_id')
                ->unique()
                ->toArray();

            $sessions = AttendanceSession::whereIn('id', $sessionIds)
                ->where('created_at', '>=', $startDate)
                ->orderBy('session_date', 'asc')
                ->get();

            for ($i = $weeks; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
                $label = $weekStart->format('d M');

                $weekSessions = $sessions->filter(function($s) use ($weekStart, $weekEnd) {
                    return Carbon::parse($s->session_date)->between($weekStart, $weekEnd);
                });

                $weekRecords = AttendanceRecord::where('student_id', $studentId)
                    ->whereIn('attendance_session_id', $weekSessions->pluck('id')->toArray())
                    ->whereIn('status', ['present', 'late'])
                    ->count();

                $totalPossible = $weekSessions->count();
                $attendance = $totalPossible > 0 ? round(($weekRecords / $totalPossible) * 100, 1) : 0;

                $trend[] = [
                    'label' => $label,
                    'attendance' => $attendance,
                ];
            }
            return $trend;
        }

        // Otherwise, university-wide trend
        $startDate = $dateFrom ? Carbon::parse($dateFrom)->subWeeks($weeks) : Carbon::now()->subWeeks($weeks);

        $totalStudents = User::where('role_id', 3)->count();
        if ($departmentId) {
            $totalStudents = User::where('role_id', 3)
                ->where('department_id', $departmentId)
                ->count();
        }

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
     * Get student attendance data for charts (AJAX)
     * ✅ FIXED: Period-based calculation per student
     */
    public function studentAttendanceData($studentId)
    {
        try {
            $student = User::with('department')->findOrFail($studentId);

            // ============================================================
            // 1. Get ALL courses the student is enrolled in (approved)
            // ============================================================
            $courseIds = Enrollment::where('student_id', $studentId)
                ->where('status', 'approved')
                ->pluck('course_id')
                ->toArray();

            if (empty($courseIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not enrolled in any courses.',
                    'weekly' => [],
                    'monthly' => [],
                    'summary' => [
                        'average_attendance' => 0,
                        'average_roll_call' => 0,
                        'overall_risk_score' => 0,
                        'risk_level' => 'Low',
                        'eligibility_status' => 'not_eligible',
                        'risk_factors' => ['No courses enrolled'],
                    ],
                    'courses' => [],
                ]);
            }

            // ============================================================
            // 2. Get all ENDED sessions for those courses (last 12 weeks)
            // ============================================================
            $weeks = 12;
            $startDate = Carbon::now()->subWeeks($weeks)->startOfWeek();

            $allSessions = AttendanceSession::whereIn('course_id', $courseIds)
                ->where('status', 'ended')
                ->where('is_cancelled', false)
                ->where('session_date', '>=', $startDate)
                ->orderBy('session_date', 'asc')
                ->get();

            if ($allSessions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance sessions found for this student.',
                    'weekly' => [],
                    'monthly' => [],
                    'summary' => [
                        'average_attendance' => 0,
                        'average_roll_call' => 0,
                        'overall_risk_score' => 0,
                        'risk_level' => 'Low',
                        'eligibility_status' => 'not_eligible',
                        'risk_factors' => ['No attendance sessions'],
                    ],
                    'courses' => [],
                ]);
            }

            // ============================================================
            // 3. Get the student's attendance records for these sessions
            // ============================================================
            $sessionIds = $allSessions->pluck('id')->toArray();
            $records = AttendanceRecord::where('student_id', $studentId)
                ->whereIn('attendance_session_id', $sessionIds)
                ->get()
                ->keyBy('attendance_session_id');

            // ============================================================
            // 4. Build weekly trend (period-based)
            // ============================================================
            $trend = [];
            $monthlyGroup = [];

            for ($i = $weeks; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
                $label = $weekStart->format('d M');

                // Sessions in this week for the student's courses
                $weekSessions = $allSessions->filter(function($s) use ($weekStart, $weekEnd) {
                    $sessionDate = Carbon::parse($s->session_date);
                    return $sessionDate->between($weekStart, $weekEnd);
                });

                if ($weekSessions->isEmpty()) {
                    $trend[] = ['label' => $label, 'attendance' => 0];
                    continue;
                }

                // Count total periods and attended periods
                $totalPeriods = 0;
                $attendedPeriods = 0;

                foreach ($weekSessions as $session) {
                    $periods = $session->conducted_periods ?? 1;
                    $totalPeriods += $periods;

                    $record = $records->get($session->id);
                    if ($record && in_array($record->status, ['present', 'late'])) {
                        $attendedPeriods += $periods;
                    }
                }

                $attendance = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100, 1) : 0;
                $trend[] = ['label' => $label, 'attendance' => $attendance];

                // Group by month for monthly summary
                $monthKey = $weekStart->format('Y-m');
                if (!isset($monthlyGroup[$monthKey])) {
                    $monthlyGroup[$monthKey] = [
                        'month' => $weekStart->format('M Y'),
                        'sum' => 0,
                        'count' => 0,
                    ];
                }
                $monthlyGroup[$monthKey]['sum'] += $attendance;
                $monthlyGroup[$monthKey]['count']++;
            }

            // Build monthly summary
            $monthly = [];
            foreach ($monthlyGroup as $key => $data) {
                $monthly[] = [
                    'month' => $data['month'],
                    'avg_attendance' => $data['count'] > 0 ? round($data['sum'] / $data['count'], 1) : 0,
                ];
            }

            // ============================================================
            // 5. Calculate overall stats from evaluations
            // ============================================================
            $evaluations = DB::table('attendance_evaluations')
                ->where('student_id', $studentId)
                ->get();

            if ($evaluations->isNotEmpty()) {
                $avgAttendance = $evaluations->avg('attendance_percentage') ?? 0;
                $avgRollCall = $evaluations->avg('roll_call_total') ?? 0;
                $overallRisk = $evaluations->avg('risk_score') ?? 0;
                $riskLevel = AttendanceHelper::getRiskLevel($overallRisk);
                $eligibility = AttendanceHelper::getEligibilityStatus($avgAttendance);

                // Collect risk factors
                $allFactors = [];
                foreach ($evaluations as $eval) {
                    $factors = json_decode($eval->risk_factors ?? '[]', true);
                    if (is_array($factors)) {
                        $allFactors = array_merge($allFactors, $factors);
                    }
                }
                $uniqueFactors = array_values(array_unique($allFactors));

                $summary = [
                    'average_attendance' => round($avgAttendance, 1),
                    'average_roll_call' => round($avgRollCall, 1),
                    'overall_risk_score' => round($overallRisk, 1),
                    'risk_level' => $riskLevel,
                    'eligibility_status' => $eligibility,
                    'risk_factors' => $uniqueFactors,
                ];

                // Per-course breakdown
                $courses = [];
                foreach ($evaluations as $eval) {
                    $course = Course::find($eval->course_id);
                    if ($course) {
                        $courses[] = [
                            'course_code' => $course->course_code,
                            'course_name' => $course->course_name,
                            'attendance' => round($eval->attendance_percentage, 1),
                            'eligibility' => $eval->eligibility_status,
                            'risk_level' => $eval->risk_level,
                            'roll_call' => round($eval->roll_call_total, 1),
                        ];
                    }
                }
            } else {
                // No evaluations – use trend data
                $avgAttendance = collect($trend)->avg('attendance') ?? 0;
                $summary = [
                    'average_attendance' => round($avgAttendance, 1),
                    'average_roll_call' => 0,
                    'overall_risk_score' => 0,
                    'risk_level' => 'Low',
                    'eligibility_status' => AttendanceHelper::getEligibilityStatus($avgAttendance),
                    'risk_factors' => ['No evaluations yet. Run attendance:evaluate.'],
                ];
                $courses = [];
            }

            // ============================================================
            // 6. Return JSON
            // ============================================================
            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'student_id' => $student->student_id ?? 'N/A',
                ],
                'weekly' => $trend,
                'monthly' => $monthly,
                'summary' => $summary,
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
