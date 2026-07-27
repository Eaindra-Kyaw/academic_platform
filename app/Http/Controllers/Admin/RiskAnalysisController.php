<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEvaluation;
use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Helpers\AttendanceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskAnalysisController extends Controller
{
    /**
     * Display risk analysis dashboard
     * ✅ FIXED: Risk Distribution and Risk Trend now count DISTINCT students
     */
    public function index(Request $request)
    {
        $departmentId = $request->input('department');
        $year = $request->input('year');
        $riskLevel = $request->input('risk_level');
        $studentId = $request->input('student_id');

        $yearLabels = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];

        $departments = Department::all();
        $students = User::where('role_id', 3)->orderBy('name')->get(['id', 'name', 'student_id']);

        // ============================================================
        // 1. Get ALL students (with filters)
        // ============================================================
        $studentQuery = User::where('role_id', 3);
        if ($departmentId) {
            $studentQuery->where('department_id', $departmentId);
        }
        if ($year) {
            $studentQuery->where('current_year', $year);
        }
        $allStudents = $studentQuery->get();
        $allStudentIds = $allStudents->pluck('id')->toArray();

        // ============================================================
        // 2. RISK DISTRIBUTION - DISTINCT STUDENTS (highest risk level)
        // ============================================================
        $studentRiskLevels = [];
        $riskCounts = ['Low' => 0, 'Medium' => 0, 'High' => 0];

        foreach ($allStudents as $student) {
            // Get all evaluations for this student (latest per course)
            $evals = AttendanceEvaluation::where('student_id', $student->id)
                ->where('evaluation_date', function($q) {
                    $q->selectRaw('MAX(evaluation_date)')
                        ->from('attendance_evaluations as ae2')
                        ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                        ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
                })
                ->get();

            if ($evals->isEmpty()) {
                continue; // No evaluations yet
            }

            // Determine overall risk level for this student (highest/majority)
            $levels = $evals->pluck('risk_level')->toArray();
            $levelCounts = array_count_values($levels);
            arsort($levelCounts);
            $overallRisk = key($levelCounts) ?? 'Low';

            $studentRiskLevels[] = [
                'student_id' => $student->id,
                'risk_level' => $overallRisk,
            ];

            if (isset($riskCounts[$overallRisk])) {
                $riskCounts[$overallRisk]++;
            }
        }

        // ============================================================
        // 3. RISK TREND - DISTINCT STUDENTS PER MONTH (last 6 months)
        // ============================================================
        $riskTrend = [];
        $months = 6;

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthCounts = ['Low' => 0, 'Medium' => 0, 'High' => 0];

            foreach ($allStudentIds as $sid) {
                // Get ALL evaluations up to this month for this student
                $allEvalsUpToMonth = AttendanceEvaluation::where('student_id', $sid)
                    ->where('evaluation_date', '<=', $monthEnd)
                    ->get();

                if ($allEvalsUpToMonth->isEmpty()) {
                    continue;
                }

                // Determine overall risk for this student up to this month (highest/majority)
                $levels = $allEvalsUpToMonth->pluck('risk_level')->toArray();
                $levelCounts = array_count_values($levels);
                arsort($levelCounts);
                $overall = key($levelCounts) ?? 'Low';

                if (isset($monthCounts[$overall])) {
                    $monthCounts[$overall]++;
                }
            }

            $riskTrend[] = [
                'month' => $month->format('M'),
                'low_risk' => $monthCounts['Low'],
                'medium_risk' => $monthCounts['Medium'],
                'high_risk' => $monthCounts['High'],
            ];
        }

        // ============================================================
        // 4. STATS (for summary cards) - using distinct students
        // ============================================================
        $stats = [
            'total_students' => count($allStudentIds),
            'total_courses' => Course::where('is_active', true)->count(),
            'low_risk' => $riskCounts['Low'],
            'medium_risk' => $riskCounts['Medium'],
            'high_risk' => $riskCounts['High'],
            'eligible' => 0,
            'warning' => 0,
            'not_eligible' => 0,
            'recovering' => 0,
            'declining' => 0,
            'critical' => 0,
        ];

        // ============================================================
        // 5. RISK DATA FOR TABLE (Medium/High risk students only)
        // ============================================================
        $riskData = [];
        $latestEval = AttendanceEvaluation::select('student_id', DB::raw('MAX(evaluation_date) as latest_date'))
            ->groupBy('student_id')
            ->pluck('latest_date', 'student_id');

        // Apply student filter if set
        $filteredStudentIds = $allStudentIds;

        foreach ($latestEval as $sid => $date) {
            // Skip if student is not in filtered list
            if (!in_array($sid, $filteredStudentIds)) {
                continue;
            }

            $eval = AttendanceEvaluation::where('student_id', $sid)
                ->where('evaluation_date', $date)
                ->first();

            if ($eval && ($eval->risk_level == 'Medium' || $eval->risk_level == 'High')) {
                // Apply risk level filter if set
                if ($riskLevel && $eval->risk_level !== $riskLevel) {
                    continue;
                }

                $riskData[] = [
                    'student' => $eval->student,
                    'attendance' => $eval->attendance_percentage,
                    'roll_call' => $eval->roll_call_total ?? 0,
                    'consistency' => $eval->consistency_marks ?? 0,
                    'punctuality' => $eval->punctuality_marks ?? 0,
                    'participation' => $eval->participation_marks ?? 0,
                    'level' => $eval->risk_level,
                    'score' => $eval->risk_score,
                    'factors' => $eval->risk_factors ?? [],
                ];
            }
        }

        usort($riskData, function($a, $b) {
            $order = ['High' => 0, 'Medium' => 1, 'Low' => 2];
            return $order[$a['level']] - $order[$b['level']];
        });

        // ============================================================
        // 6. DEPARTMENT RISK DISTRIBUTION (for bars - optional)
        // ============================================================
        $riskByDepartment = Department::with(['courses' => function($q) {
            $q->with(['evaluations' => function($q2) {
                $q2->where('evaluation_date', function($q3) {
                    $q3->selectRaw('MAX(evaluation_date)')
                        ->from('attendance_evaluations as ae2')
                        ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                        ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
                });
            }]);
        }])->get()->map(function($dept) {
            $evals = $dept->courses->flatMap->evaluations;
            return [
                'name' => $dept->code,
                'total' => $evals->count(),
                'Low' => $evals->where('risk_level', 'Low')->count(),
                'Medium' => $evals->where('risk_level', 'Medium')->count(),
                'High' => $evals->where('risk_level', 'High')->count(),
            ];
        })->filter(function($item) {
            return $item['total'] > 0;
        })->values();

        // ============================================================
        // 7. RISK FACTORS (for future use)
        // ============================================================
        $riskFactors = [];

        // ============================================================
        // 8. RETURN VIEW
        // ============================================================
        return view('admin.risk.index', compact(
            'departments',
            'students',
            'stats',
            'riskCounts',
            'riskFactors',
            'riskTrend',
            'riskData',
            'riskByDepartment',
            'departmentId',
            'year',
            'riskLevel',
            'studentId',
            'yearLabels'
        ));
    }

    /**
     * Get student risk history (weekly/monthly) - PERIOD-BASED
     */
    public function studentRiskHistory($studentId)
    {
        try {
            $student = User::findOrFail($studentId);

            // Get all courses the student is enrolled in
            $courseIds = Enrollment::where('student_id', $studentId)
                ->where('status', 'approved')
                ->pluck('course_id')
                ->toArray();

            if (empty($courseIds)) {
                return response()->json([
                    'success' => true,
                    'student' => $student->only(['id', 'name', 'student_id']),
                    'weekly' => [],
                    'monthly' => [],
                ]);
            }

            // Get all ended sessions for those courses (last 12 weeks)
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
                    'success' => true,
                    'student' => $student->only(['id', 'name', 'student_id']),
                    'weekly' => [],
                    'monthly' => [],
                ]);
            }

            $sessionIds = $allSessions->pluck('id')->toArray();
            $records = AttendanceRecord::where('student_id', $studentId)
                ->whereIn('attendance_session_id', $sessionIds)
                ->get()
                ->keyBy('attendance_session_id');

            // Build weekly trend (period-based)
            $weekly = [];
            $monthlyGroup = [];

            for ($i = $weeks; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
                $label = $weekStart->format('d M');

                $weekSessions = $allSessions->filter(function($s) use ($weekStart, $weekEnd) {
                    return Carbon::parse($s->session_date)->between($weekStart, $weekEnd);
                });

                if ($weekSessions->isEmpty()) {
                    $weekly[] = ['label' => $label, 'attendance' => 0, 'risk_score' => 0];
                    continue;
                }

                $totalPeriods = 0;
                $attendedPeriods = 0;
                $riskSum = 0;
                $riskCount = 0;

                foreach ($weekSessions as $session) {
                    $periods = $session->conducted_periods ?? 1;
                    $totalPeriods += $periods;

                    $record = $records->get($session->id);
                    if ($record && in_array($record->status, ['present', 'late'])) {
                        $attendedPeriods += $periods;
                    }

                    // Get evaluation for this course (latest)
                    $eval = AttendanceEvaluation::where('student_id', $studentId)
                        ->where('course_id', $session->course_id)
                        ->orderBy('evaluation_date', 'desc')
                        ->first();

                    if ($eval) {
                        $riskSum += $eval->risk_score;
                        $riskCount++;
                    }
                }

                $attendance = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100, 1) : 0;
                $avgRisk = $riskCount > 0 ? round($riskSum / $riskCount, 1) : 0;

                $weekly[] = [
                    'label' => $label,
                    'attendance' => $attendance,
                    'risk_score' => $avgRisk,
                ];

                // Monthly grouping
                $monthKey = $weekStart->format('Y-m');
                if (!isset($monthlyGroup[$monthKey])) {
                    $monthlyGroup[$monthKey] = [
                        'label' => $weekStart->format('M Y'),
                        'attendance_sum' => 0,
                        'risk_sum' => 0,
                        'count' => 0,
                    ];
                }
                $monthlyGroup[$monthKey]['attendance_sum'] += $attendance;
                $monthlyGroup[$monthKey]['risk_sum'] += $avgRisk;
                $monthlyGroup[$monthKey]['count']++;
            }

            $monthly = [];
            foreach ($monthlyGroup as $key => $data) {
                $monthly[] = [
                    'label' => $data['label'],
                    'attendance' => $data['count'] > 0 ? round($data['attendance_sum'] / $data['count'], 1) : 0,
                    'risk_score' => $data['count'] > 0 ? round($data['risk_sum'] / $data['count'], 1) : 0,
                ];
            }

            return response()->json([
                'success' => true,
                'student' => $student->only(['id', 'name', 'student_id']),
                'weekly' => $weekly,
                'monthly' => $monthly,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get risk details for a specific student
     */
    public function studentRisk($studentId)
    {
        $student = User::findOrFail($studentId);

        $evaluations = AttendanceEvaluation::where('student_id', $studentId)
            ->with(['course.department'])
            ->orderBy('evaluation_date', 'desc')
            ->get();

        $latest = $evaluations->first();

        $summary = [
            'student' => $student,
            'latest_evaluation' => $latest,
            'total_courses' => $evaluations->count(),
            'avg_attendance' => round($evaluations->avg('attendance_percentage'), 1),
            'avg_risk_score' => round($evaluations->avg('risk_score'), 1),
            'overall_risk' => $this->getOverallRisk($evaluations),
            'overall_eligibility' => $this->getOverallEligibility($evaluations),
            'courses' => $evaluations->map(function($e) {
                return [
                    'course' => $e->course,
                    'attendance' => $e->attendance_percentage,
                    'risk_level' => $e->risk_level,
                    'risk_score' => $e->risk_score,
                    'eligibility' => $e->eligibility_status,
                    'recovery' => $e->recovery_status,
                    'date' => $e->evaluation_date,
                ];
            }),
            'risk_history' => $this->getRiskHistory($studentId),
        ];

        return view('admin.risk-analysis.student', compact('summary'));
    }

    /**
     * Get risk by department
     */
    public function departmentRisk($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        $evaluations = AttendanceEvaluation::whereHas('course', function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->where('evaluation_date', function($q) {
            $q->selectRaw('MAX(evaluation_date)')
                ->from('attendance_evaluations as ae2')
                ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
        })->get();

        $stats = [
            'total' => $evaluations->count(),
            'low_risk' => $evaluations->where('risk_level', 'Low')->count(),
            'medium_risk' => $evaluations->where('risk_level', 'Medium')->count(),
            'high_risk' => $evaluations->where('risk_level', 'High')->count(),
            'eligible' => $evaluations->where('eligibility_status', 'Eligible')->count(),
            'warning' => $evaluations->where('eligibility_status', 'Warning')->count(),
            'not_eligible' => $evaluations->where('eligibility_status', 'Not Eligible')->count(),
        ];

        $courses = Course::where('department_id', $departmentId)
            ->with(['evaluations' => function($q) {
                $q->where('evaluation_date', function($q2) {
                    $q2->selectRaw('MAX(evaluation_date)')
                        ->from('attendance_evaluations as ae2')
                        ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                        ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
                });
            }])
            ->get();

        return view('admin.risk-analysis.department', compact('department', 'stats', 'courses'));
    }

    /**
     * Export risk report
     */
    public function export(Request $request)
    {
        $departmentId = $request->input('department');
        $riskLevel = $request->input('risk_level');

        $query = AttendanceEvaluation::with(['student', 'course.department'])
            ->where('evaluation_date', function($q) {
                $q->selectRaw('MAX(evaluation_date)')
                    ->from('attendance_evaluations as ae2')
                    ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                    ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
            });

        if ($departmentId) {
            $query->whereHas('course', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($riskLevel) {
            $query->where('risk_level', $riskLevel);
        }

        $evaluations = $query->get();

        $filename = 'risk_report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($evaluations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Student ID', 'Student Name', 'Email', 'Year',
                'Course Code', 'Course Name',
                'Attendance %', 'Roll Call', 'Consistency', 'Punctuality', 'Participation',
                'Risk Level', 'Risk Score',
                'Eligibility', 'Recovery Status'
            ]);

            foreach ($evaluations as $e) {
                fputcsv($handle, [
                    $e->student->student_id ?? 'N/A',
                    $e->student->name,
                    $e->student->email,
                    $e->student->current_year ?? 'N/A',
                    $e->course->course_code,
                    $e->course->course_name,
                    $e->attendance_percentage,
                    $e->roll_call_total ?? 0,
                    $e->consistency_marks ?? 0,
                    $e->punctuality_marks ?? 0,
                    $e->participation_marks ?? 0,
                    $e->risk_level,
                    $e->risk_score,
                    $e->eligibility_status,
                    $e->recovery_status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Helper methods
    private function getOverallRisk($evaluations)
    {
        if ($evaluations->isEmpty()) return 'Low';
        $avgRisk = $evaluations->avg('risk_score');
        if ($avgRisk < 40) return 'Low';
        if ($avgRisk < 70) return 'Medium';
        return 'High';
    }

    private function getOverallEligibility($evaluations)
    {
        if ($evaluations->isEmpty()) return 'Eligible';
        $notEligible = $evaluations->where('eligibility_status', 'Not Eligible')->count();
        $warning = $evaluations->where('eligibility_status', 'Warning')->count();
        if ($notEligible > 0) return 'Not Eligible';
        if ($warning > 0) return 'Warning';
        return 'Eligible';
    }

    private function getRiskHistory($studentId)
    {
        return AttendanceEvaluation::where('student_id', $studentId)
            ->with('course')
            ->orderBy('evaluation_date', 'asc')
            ->get()
            ->groupBy('evaluation_date')
            ->map(function($group, $date) {
                return [
                    'date' => $date,
                    'avg_risk' => round($group->avg('risk_score'), 1),
                    'avg_attendance' => round($group->avg('attendance_percentage'), 1),
                    'low' => $group->where('risk_level', 'Low')->count(),
                    'medium' => $group->where('risk_level', 'Medium')->count(),
                    'high' => $group->where('risk_level', 'High')->count(),
                ];
            })
            ->values();
    }
}
