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
     * Display risk analysis dashboard with course filter
     */
    public function index(Request $request)
    {
        $departmentId = $request->input('department');
        $courseId = $request->input('course_id');
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

        // Get all courses for filter (filtered by department if selected)
        $coursesQuery = Course::where('is_active', true);
        if ($departmentId) {
            $coursesQuery->where('department_id', $departmentId);
        }
        $courses = $coursesQuery->orderBy('course_code')->get();

        $students = User::where('role_id', 3)->orderBy('name')->get(['id', 'name', 'student_id']);

        // ============================================================
        // 1. Get all students (with filters)
        // ============================================================
        $studentQuery = User::where('role_id', 3);
        if ($departmentId) {
            $studentQuery->where('department_id', $departmentId);
        }
        if ($year) {
            $studentQuery->where('current_year', $year);
        }
        if ($courseId) {
            // Only students enrolled in this course
            $enrolledStudentIds = Enrollment::where('course_id', $courseId)
                ->where('status', 'approved')
                ->pluck('student_id')
                ->toArray();
            $studentQuery->whereIn('id', $enrolledStudentIds);
        }
        $allStudents = $studentQuery->get();
        $allStudentIds = $allStudents->pluck('id')->toArray();

        // ============================================================
        // 2. Risk Distribution - DISTINCT STUDENTS
        // ============================================================
        $riskCounts = ['Low' => 0, 'Medium' => 0, 'High' => 0];

        foreach ($allStudents as $student) {
            // If course filter is applied, only get evaluations for that course
            $evalQuery = AttendanceEvaluation::where('student_id', $student->id)
                ->where('evaluation_date', function($q) {
                    $q->selectRaw('MAX(evaluation_date)')
                        ->from('attendance_evaluations as ae2')
                        ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                        ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
                });

            if ($courseId) {
                $evalQuery->where('course_id', $courseId);
            }

            $evals = $evalQuery->get();

            if ($evals->isEmpty()) continue;

            $levels = $evals->pluck('risk_level')->toArray();
            $levelCounts = array_count_values($levels);
            arsort($levelCounts);
            $overallRisk = key($levelCounts) ?? 'Low';

            if (isset($riskCounts[$overallRisk])) $riskCounts[$overallRisk]++;
        }

        // ============================================================
        // 3. Risk Trend (6 months) - DISTINCT STUDENTS
        // ============================================================
        $riskTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthEnd = $month->copy()->endOfMonth();
            $counts = ['Low' => 0, 'Medium' => 0, 'High' => 0];

            foreach ($allStudentIds as $sid) {
                $evalQuery = AttendanceEvaluation::where('student_id', $sid)
                    ->where('evaluation_date', '<=', $monthEnd);

                if ($courseId) {
                    $evalQuery->where('course_id', $courseId);
                }

                $allEvals = $evalQuery->get();
                if ($allEvals->isEmpty()) continue;

                $levels = $allEvals->pluck('risk_level')->toArray();
                $levelCounts = array_count_values($levels);
                arsort($levelCounts);
                $overall = key($levelCounts) ?? 'Low';
                if (isset($counts[$overall])) $counts[$overall]++;
            }

            $riskTrend[] = [
                'month' => $month->format('M'),
                'low_risk' => $counts['Low'],
                'medium_risk' => $counts['Medium'],
                'high_risk' => $counts['High'],
            ];
        }

        // ============================================================
        // 4. Stats for cards
        // ============================================================
        $stats = [
            'total_students' => count($allStudentIds),
            'low_risk' => $riskCounts['Low'],
            'medium_risk' => $riskCounts['Medium'],
            'high_risk' => $riskCounts['High'],
        ];

        // ============================================================
        // 5. Overall Alerts (Medium/High risk students)
        // ============================================================
        $overallAlerts = [];
        $latestEval = AttendanceEvaluation::select('student_id', DB::raw('MAX(evaluation_date) as latest_date'))
            ->groupBy('student_id')
            ->pluck('latest_date', 'student_id');

        foreach ($latestEval as $sid => $date) {
            if (!in_array($sid, $allStudentIds)) continue;

            $evalQuery = AttendanceEvaluation::where('student_id', $sid)
                ->where('evaluation_date', $date);

            if ($courseId) {
                $evalQuery->where('course_id', $courseId);
            }

            $eval = $evalQuery->first();

            if ($eval && in_array($eval->risk_level, ['Medium', 'High'])) {
                if ($riskLevel && $eval->risk_level !== $riskLevel) continue;

                $overallAlerts[] = [
                    'student' => $eval->student,
                    'course' => $eval->course,
                    'level' => $eval->risk_level,
                    'attendance' => $eval->attendance_percentage,
                ];
            }
        }

        usort($overallAlerts, function($a, $b) {
            $order = ['High' => 0, 'Medium' => 1];
            return $order[$a['level']] - $order[$b['level']];
        });

        // ============================================================
        // 6. Weekly Alerts - ALL STUDENTS (with current risk level)
        // ============================================================
        $weeklyAlerts = $this->getWeeklyAlerts($allStudentIds, $courseId);

        // ============================================================
        // 7. Monthly Alerts - ALL STUDENTS (with current risk level)
        // ============================================================
        $monthlyAlerts = $this->getMonthlyAlerts($allStudentIds, $courseId);

        // ============================================================
        // 8. VIEW
        // ============================================================
        return view('admin.risk.index', compact(
            'departments',
            'courses',
            'students',
            'stats',
            'riskCounts',
            'riskTrend',
            'overallAlerts',
            'weeklyAlerts',
            'monthlyAlerts',
            'departmentId',
            'courseId',
            'year',
            'riskLevel',
            'studentId',
            'yearLabels'
        ));
    }

    /**
     * Get weekly alerts for all students (with current risk level)
     */
    private function getWeeklyAlerts($studentIds, $courseId = null)
    {
        $alerts = [];
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monday = Carbon::now()->startOfWeek();
        $friday = Carbon::now()->startOfWeek()->addDays(4);

        foreach ($studentIds as $sid) {
            // Get this week's risk level
            $query = AttendanceEvaluation::where('student_id', $sid)
                ->whereBetween('evaluation_date', [$weekStart, $weekEnd]);
            if ($courseId) {
                $query->where('course_id', $courseId);
            }
            $thisWeekEvals = $query->get();

            if ($thisWeekEvals->isNotEmpty()) {
                $levels = $thisWeekEvals->pluck('risk_level')->toArray();
                $levelCounts = array_count_values($levels);
                arsort($levelCounts);
                $currentLevel = key($levelCounts) ?? 'Low';
            } else {
                // If no evaluation this week, use the latest overall evaluation
                $latestQuery = AttendanceEvaluation::where('student_id', $sid);
                if ($courseId) {
                    $latestQuery->where('course_id', $courseId);
                }
                $latestEval = $latestQuery->orderBy('evaluation_date', 'desc')->first();
                $currentLevel = $latestEval ? $latestEval->risk_level : 'Low';
            }

            // Only include students with Medium/High risk (since we only show at-risk students)
            if ($currentLevel !== 'Medium' && $currentLevel !== 'High') continue;

            $student = User::find($sid);
            if ($student) {
                $alerts[] = [
                    'student' => $student,
                    'current_level' => $currentLevel,
                    'period_label' => $monday->format('M d') . ' - ' . $friday->format('M d'),
                ];
            }
        }

        // Sort: High first, then Medium
        usort($alerts, function($a, $b) {
            $order = ['High' => 0, 'Medium' => 1];
            return $order[$a['current_level']] - $order[$b['current_level']];
        });

        return $alerts;
    }

    /**
     * Get monthly alerts for all students (with current risk level)
     */
    private function getMonthlyAlerts($studentIds, $courseId = null)
    {
        $alerts = [];
        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();

        foreach ($studentIds as $sid) {
            // Get this month's risk level
            $query = AttendanceEvaluation::where('student_id', $sid)
                ->whereBetween('evaluation_date', [$thisMonthStart, $thisMonthEnd]);
            if ($courseId) {
                $query->where('course_id', $courseId);
            }
            $thisMonthEvals = $query->get();

            if ($thisMonthEvals->isNotEmpty()) {
                $levels = $thisMonthEvals->pluck('risk_level')->toArray();
                $levelCounts = array_count_values($levels);
                arsort($levelCounts);
                $currentLevel = key($levelCounts) ?? 'Low';
            } else {
                // If no evaluation this month, use the latest overall evaluation
                $latestQuery = AttendanceEvaluation::where('student_id', $sid);
                if ($courseId) {
                    $latestQuery->where('course_id', $courseId);
                }
                $latestEval = $latestQuery->orderBy('evaluation_date', 'desc')->first();
                $currentLevel = $latestEval ? $latestEval->risk_level : 'Low';
            }

            // Only include students with Medium/High risk
            if ($currentLevel !== 'Medium' && $currentLevel !== 'High') continue;

            $student = User::find($sid);
            if ($student) {
                $alerts[] = [
                    'student' => $student,
                    'current_level' => $currentLevel,
                    'period_label' => $thisMonthStart->format('M Y'),
                ];
            }
        }

        usort($alerts, function($a, $b) {
            $order = ['High' => 0, 'Medium' => 1];
            return $order[$a['current_level']] - $order[$b['current_level']];
        });

        return $alerts;
    }

    /**
     * Get student risk history for modal AJAX
     */
    public function studentRiskHistory($studentId)
    {
        try {
            $student = User::findOrFail($studentId);

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
                    $weekly[] = ['label' => $label, 'attendance' => 0, 'risk_level' => 'Low'];
                    continue;
                }

                $totalPeriods = 0;
                $attendedPeriods = 0;
                $riskLevels = [];

                foreach ($weekSessions as $session) {
                    $periods = $session->conducted_periods ?? 1;
                    $totalPeriods += $periods;

                    $record = $records->get($session->id);
                    if ($record && in_array($record->status, ['present', 'late'])) {
                        $attendedPeriods += $periods;
                    }

                    $eval = AttendanceEvaluation::where('student_id', $studentId)
                        ->where('course_id', $session->course_id)
                        ->orderBy('evaluation_date', 'desc')
                        ->first();

                    if ($eval) {
                        $riskLevels[] = $eval->risk_level;
                    }
                }

                $attendance = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100, 1) : 0;

                $riskLevel = 'Low';
                if (!empty($riskLevels)) {
                    $levelCounts = array_count_values($riskLevels);
                    arsort($levelCounts);
                    $riskLevel = key($levelCounts) ?? 'Low';
                }

                $weekly[] = [
                    'label' => $label,
                    'attendance' => $attendance,
                    'risk_level' => $riskLevel,
                ];

                $monthKey = $weekStart->format('Y-m');
                if (!isset($monthlyGroup[$monthKey])) {
                    $monthlyGroup[$monthKey] = [
                        'label' => $weekStart->format('M Y'),
                        'attendance_sum' => 0,
                        'risk_levels' => [],
                        'count' => 0,
                    ];
                }
                $monthlyGroup[$monthKey]['attendance_sum'] += $attendance;
                if (!empty($riskLevels)) {
                    $monthlyGroup[$monthKey]['risk_levels'] = array_merge($monthlyGroup[$monthKey]['risk_levels'], $riskLevels);
                }
                $monthlyGroup[$monthKey]['count']++;
            }

            $monthly = [];
            foreach ($monthlyGroup as $key => $data) {
                $avgAttendance = $data['count'] > 0 ? round($data['attendance_sum'] / $data['count'], 1) : 0;
                $riskLevel = 'Low';
                if (!empty($data['risk_levels'])) {
                    $levelCounts = array_count_values($data['risk_levels']);
                    arsort($levelCounts);
                    $riskLevel = key($levelCounts) ?? 'Low';
                }

                $monthly[] = [
                    'label' => $data['label'],
                    'attendance' => $avgAttendance,
                    'risk_level' => $riskLevel,
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

    // ============================================================
    // Legacy methods – kept for backward compatibility
    // ============================================================

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
            'overall_risk' => $this->getOverallRisk($evaluations),
            'overall_eligibility' => $this->getOverallEligibility($evaluations),
            'courses' => $evaluations->map(function($e) {
                return [
                    'course' => $e->course,
                    'attendance' => $e->attendance_percentage,
                    'risk_level' => $e->risk_level,
                    'eligibility' => $e->eligibility_status,
                    'recovery' => $e->recovery_status,
                    'date' => $e->evaluation_date,
                ];
            }),
            'risk_history' => $this->getRiskHistory($studentId),
        ];

        return view('admin.risk-analysis.student', compact('summary'));
    }

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
                'Attendance %', 'Risk Level',
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
                    $e->risk_level,
                    $e->eligibility_status,
                    $e->recovery_status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ============================================================
    // Helper methods
    // ============================================================

    private function getOverallRisk($evaluations)
    {
        if ($evaluations->isEmpty()) return 'Low';
        $levels = $evaluations->pluck('risk_level')->toArray();
        $levelCounts = array_count_values($levels);
        arsort($levelCounts);
        return key($levelCounts) ?? 'Low';
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
                $levels = $group->pluck('risk_level')->toArray();
                $levelCounts = array_count_values($levels);
                arsort($levelCounts);
                $overall = key($levelCounts) ?? 'Low';
                return [
                    'date' => $date,
                    'avg_risk' => $overall,
                    'avg_attendance' => round($group->avg('attendance_percentage'), 1),
                    'low' => $group->where('risk_level', 'Low')->count(),
                    'medium' => $group->where('risk_level', 'Medium')->count(),
                    'high' => $group->where('risk_level', 'High')->count(),
                ];
            })
            ->values();
    }
}
