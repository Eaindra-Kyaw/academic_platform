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
    public function index(Request $request)
    {
        $currentSemester = Semester::where('is_current', true)->first();
        $selectedSemester = $request->input('semester_id', $currentSemester ? $currentSemester->id : null);

        $departmentId = $request->input('department_id');
        $courseId = $request->input('course_id');
        $year = $request->input('year');
        $studentId = $request->input('student_id');
        $dateRange = $request->input('date_range', 'this_month');

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

        // 1. STATISTICS
        $stats = $this->getStats($departmentId, $courseId, $year, $dateFrom, $dateTo);

        // Add filtered student and course counts
        $filteredStudents = User::where('role_id', 3);
        if ($departmentId) {
            $filteredStudents->where('department_id', $departmentId);
        }
        if ($year) {
            $filteredStudents->where('current_year', $year);
        }
        if ($courseId) {
            $filteredStudents->whereHas('enrollments', function ($q) use ($courseId) {
                $q->where('course_id', $courseId)->where('status', 'approved');
            });
        }
        $stats['total_students'] = $filteredStudents->count();

        $filteredCourses = Course::where('is_active', true);
        if ($departmentId) {
            $filteredCourses->where('department_id', $departmentId);
        }
        $stats['total_courses'] = $filteredCourses->count();

        // 2. DEPARTMENT ATTENDANCE
        $departmentAttendance = $this->getDepartmentAttendance($departmentId, $dateFrom, $dateTo);

        // 3. COURSE RANKING – now applies course filter
        $courseRanking = $this->getCourseRanking($departmentId, $courseId, $dateFrom, $dateTo);

        // 4. AT-RISK STUDENTS (hidden but kept)
        $atRiskStudents = $this->getAtRiskStudents($departmentId, $year);

        // 5. ELIGIBILITY STATS (not used)
        $eligibilityStats = $this->getEligibilityStats($departmentId);

        $yearLabels = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];

        $departments = Department::orderBy('name')->get();
        $courses = Course::where('is_active', true)->orderBy('course_code')->get();
        $students = User::where('role_id', 3)->orderBy('name')->get(['id', 'name', 'student_id']);
        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_number', 'asc')
            ->get();

        return view('admin.attendance.analytics', compact(
            'stats',
            'departmentAttendance',
            'courseRanking',
            'atRiskStudents',
            'eligibilityStats',
            'departments',
            'courses',
            'students',
            'semesters',
            'selectedSemester',
            'departmentId',
            'courseId',
            'year',
            'studentId',
            'dateRange',
            'dateFrom',
            'dateTo',
            'yearLabels'
        ));
    }

    /**
     * Get statistics from attendance evaluations (filtered)
     */
    private function getStats($departmentId = null, $courseId = null, $year = null, $dateFrom = null, $dateTo = null)
    {
        $query = DB::table('attendance_evaluations')
            ->join('users', 'users.id', '=', 'attendance_evaluations.student_id')
            ->join('courses', 'courses.id', '=', 'attendance_evaluations.course_id')
            ->where('users.role_id', 3);

        if ($departmentId) {
            $query->where('courses.department_id', $departmentId);
        }
        if ($courseId) {
            $query->where('attendance_evaluations.course_id', $courseId);
        }
        if ($year) {
            $query->where('users.current_year', $year);
        }
        if ($dateFrom) {
            $query->whereDate('evaluation_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('evaluation_date', '<=', $dateTo);
        }

        $totalRecords = $query->count();
        $avgAttendance = round($query->avg('attendance_percentage') ?? 0, 1);
        $totalSessions = $query->sum('total_sessions');
        $presentCount = $query->sum('attended_sessions');

        return [
            'total_sessions' => $totalSessions,
            'total_records' => $totalRecords,
            'avg_attendance' => $avgAttendance,
            'present_count' => $presentCount,
        ];
    }

    /**
     * Department attendance – shows ALL departments with valid codes.
     * Departments with no students show 0% attendance.
     */
    private function getDepartmentAttendance($filterDepartmentId = null, $dateFrom = null, $dateTo = null)
    {
        $departments = Department::whereNotNull('code')
            ->where('code', '!=', '')
            ->orderBy('code')
            ->get();

        $result = [];

        foreach ($departments as $dept) {
            if ($filterDepartmentId && $dept->id != $filterDepartmentId) {
                continue;
            }

            $students = User::where('role_id', 3)
                ->where('department_id', $dept->id)
                ->pluck('id');

            if ($students->isEmpty()) {
                $result[] = [
                    'id' => $dept->id,
                    'name' => $dept->code,
                    'attendance' => 0,
                    'total_students' => 0,
                ];
                continue;
            }

            $evalQuery = DB::table('attendance_evaluations')
                ->whereIn('student_id', $students);
            if ($dateFrom) {
                $evalQuery->whereDate('evaluation_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $evalQuery->whereDate('evaluation_date', '<=', $dateTo);
            }

            $avgAtt = $evalQuery->avg('attendance_percentage') ?? 0;

            $result[] = [
                'id' => $dept->id,
                'name' => $dept->code,
                'attendance' => round($avgAtt, 1),
                'total_students' => $students->count(),
            ];
        }

        usort($result, function ($a, $b) {
            return $b['attendance'] - $a['attendance'];
        });

        return $result;
    }

    /**
     * Get course attendance ranking – PERIOD‑BASED and respects course filter.
     */
    private function getCourseRanking($departmentId = null, $courseId = null, $dateFrom = null, $dateTo = null)
    {
        $query = Course::with('department')
            ->where('is_active', true);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        if ($courseId) {
            $query->where('id', $courseId);
        }

        $courses = $query->get();
        $ranking = [];

        foreach ($courses as $course) {
            // Count approved enrolled students (all time)
            $totalStudents = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count();

            // Get sessions in the date range (using session_date)
            $sessionQuery = AttendanceSession::where('course_id', $course->id)
                ->where('status', 'ended')
                ->where('is_cancelled', false);

            if ($dateFrom) {
                $sessionQuery->whereDate('session_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $sessionQuery->whereDate('session_date', '<=', $dateTo);
            }

            $sessions = $sessionQuery->get();
            if ($sessions->isEmpty()) {
                continue; // skip courses with no sessions
            }

            // Calculate total periods and attended periods
            $totalPeriods = $sessions->sum('conducted_periods') ?: 0;
            if ($totalPeriods == 0) {
                continue;
            }

            $sessionIds = $sessions->pluck('id')->toArray();

            // For each session, count present/late records
            // attended periods = sum(conducted_periods * number of present/late records)
            $attendedPeriods = 0;
            foreach ($sessions as $session) {
                $recordsCount = AttendanceRecord::where('attendance_session_id', $session->id)
                    ->whereIn('status', ['present', 'late'])
                    ->count();
                $attendedPeriods += $session->conducted_periods * $recordsCount;
            }

            $expectedPeriods = $totalPeriods * $totalStudents;
            $attendance = $expectedPeriods > 0 ? round(($attendedPeriods / $expectedPeriods) * 100, 1) : 0;

            $ranking[] = [
                'course_id'    => $course->id,
                'course_code'  => $course->course_code,
                'course_name'  => $course->course_name,
                'department'   => $course->department->code ?? 'N/A',
                'attendance'   => $attendance,
                'students'     => $totalStudents,
                'sessions'     => $sessions->count(),      // number of class sessions
                'periods'      => $totalPeriods,           // total conducted periods
                'records'      => $attendedPeriods,        // total attended periods (sum over students)
            ];
        }

        usort($ranking, function ($a, $b) {
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

        usort($result, function ($a, $b) {
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
            $query->whereHas('course', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $enrollments = $query->get();

        $eligible = $enrollments->filter(function ($e) {
            return $e->eligibility_status === 'eligible';
        })->count();

        $warning = $enrollments->filter(function ($e) {
            return $e->eligibility_status === 'warning';
        })->count();

        $notEligible = $enrollments->filter(function ($e) {
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
     * Get student attendance data for charts (AJAX) – period‑based, respects date filters.
     */
    public function studentAttendanceData($studentId, Request $request)
    {
        try {
            $student = User::with('department')->findOrFail($studentId);

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

            // Get date range from request, fallback to last 12 weeks
            $dateFrom = $request->input('date_from');
            $dateTo   = $request->input('date_to');
            if (!$dateFrom) {
                $dateFrom = Carbon::now()->subWeeks(12)->startOfWeek();
            } else {
                $dateFrom = Carbon::parse($dateFrom);
            }
            if ($dateTo) {
                $dateTo = Carbon::parse($dateTo);
            } else {
                $dateTo = Carbon::now();
            }

            // Fetch all sessions for the student's courses within the date range
            $allSessions = AttendanceSession::whereIn('course_id', $courseIds)
                ->where('status', 'ended')
                ->where('is_cancelled', false)
                ->whereDate('session_date', '>=', $dateFrom)
                ->whereDate('session_date', '<=', $dateTo)
                ->orderBy('session_date', 'asc')
                ->get();

            if ($allSessions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance sessions found for this student in the selected period.',
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

            $sessionIds = $allSessions->pluck('id')->toArray();
            $records = AttendanceRecord::where('student_id', $studentId)
                ->whereIn('attendance_session_id', $sessionIds)
                ->get()
                ->keyBy('attendance_session_id');

            // Build weekly trend (period‑based)
            $weeks = 12;
            $trend = [];
            $monthlyGroup = [];

            $startDate = Carbon::parse($dateFrom)->startOfWeek();
            $endDate = Carbon::parse($dateTo)->endOfWeek();
            $currentWeek = $startDate->copy();

            while ($currentWeek <= $endDate) {
                $weekStart = $currentWeek->copy()->startOfWeek();
                $weekEnd = $currentWeek->copy()->endOfWeek();
                $label = $weekStart->format('d M');

                $weekSessions = $allSessions->filter(function ($s) use ($weekStart, $weekEnd) {
                    $sessionDate = Carbon::parse($s->session_date);
                    return $sessionDate->between($weekStart, $weekEnd);
                });

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
                $trend[] = [
                    'label' => $label,
                    'attendance' => $attendance,
                ];

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

                $currentWeek->addWeek();
            }

            $monthly = [];
            foreach ($monthlyGroup as $key => $data) {
                $monthly[] = [
                    'month' => $data['month'],
                    'avg_attendance' => $data['count'] > 0 ? round($data['sum'] / $data['count'], 1) : 0,
                ];
            }

            // Summary from evaluations (if any)
            $evaluations = DB::table('attendance_evaluations')
                ->where('student_id', $studentId)
                ->get();

            if ($evaluations->isNotEmpty()) {
                $avgAttendance = $evaluations->avg('attendance_percentage') ?? 0;
                $avgRollCall = $evaluations->avg('roll_call_total') ?? 0;
                $overallRisk = $evaluations->avg('risk_score') ?? 0;
                $riskLevel = AttendanceHelper::getRiskLevel($overallRisk);
                $eligibility = AttendanceHelper::getEligibilityStatus($avgAttendance);

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
     * Get students of a specific course with their attendance – PERIOD-BASED.
     */
    public function courseStudents($courseId, Request $request)
    {
        try {
            $course = Course::findOrFail($courseId);
            $dateFrom = $request->input('date_from');
            $dateTo   = $request->input('date_to');

            // Get all approved enrollments for this course
            $enrollments = Enrollment::where('course_id', $courseId)
                ->where('status', 'approved')
                ->with('student')
                ->get();

            if ($enrollments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students enrolled in this course.',
                    'students' => [],
                ]);
            }

            // Get all sessions for this course in the date range
            $sessionQuery = AttendanceSession::where('course_id', $courseId)
                ->where('status', 'ended')
                ->where('is_cancelled', false);
            if ($dateFrom) {
                $sessionQuery->whereDate('session_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $sessionQuery->whereDate('session_date', '<=', $dateTo);
            }
            $sessions = $sessionQuery->get();
            $sessionIds = $sessions->pluck('id')->toArray();

            if (empty($sessionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sessions held for this course in the selected period.',
                    'students' => [],
                ]);
            }

            $totalPeriods = $sessions->sum('conducted_periods') ?: 0;
            if ($totalPeriods == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total periods is zero.',
                    'students' => [],
                ]);
            }

            $periodsMap = $sessions->pluck('conducted_periods', 'id')->toArray();

            $studentData = [];
            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;
                if (!$student) continue;

                $records = AttendanceRecord::where('student_id', $student->id)
                    ->whereIn('attendance_session_id', $sessionIds)
                    ->whereIn('status', ['present', 'late'])
                    ->get()
                    ->keyBy('attendance_session_id');

                $attendedPeriods = 0;
                foreach ($periodsMap as $sessionId => $periods) {
                    if ($records->has($sessionId)) {
                        $attendedPeriods += $periods;
                    }
                }

                $attendance = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100, 1) : 0;

                $studentData[] = [
                    'id' => $student->id,
                    'name' => $student->name,
                    'student_id' => $student->student_id ?? 'N/A',
                    'attendance' => $attendance,
                    'records' => $attendedPeriods,
                ];
            }

            usort($studentData, function ($a, $b) {
                return $b['attendance'] - $a['attendance'];
            });

            return response()->json([
                'success' => true,
                'course' => [
                    'code' => $course->course_code,
                    'name' => $course->course_name,
                ],
                'total_students' => count($studentData),
                'total_sessions' => $sessions->count(),
                'total_periods' => $totalPeriods,
                'students' => $studentData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function records(Request $request)
    {
        $query = AttendanceRecord::with(['session.course', 'student']);

        if ($request->filled('department_id')) {
            $query->whereHas('session.course', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('course_id')) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
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
