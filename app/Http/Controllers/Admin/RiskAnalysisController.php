<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEvaluation;
use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskAnalysisController extends Controller
{
    /**
     * Display risk analysis dashboard
     */
    public function index(Request $request)
    {
        $departmentId = $request->input('department');
        $year = $request->input('year');
        $riskLevel = $request->input('risk_level');

        // Year labels for filter
        $yearLabels = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];

        // Get all departments for filter
        $departments = Department::all();

        // Build query for evaluations
        $query = AttendanceEvaluation::with(['student', 'course.department'])
            ->where('evaluation_date', function($q) {
                $q->selectRaw('MAX(evaluation_date)')
                    ->from('attendance_evaluations as ae2')
                    ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                    ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
            });

        // Apply filters
        if ($departmentId) {
            $query->whereHas('course', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($year) {
            $query->whereHas('student', function($q) use ($year) {
                $q->where('current_year', $year);
            });
        }

        if ($riskLevel) {
            $query->where('risk_level', $riskLevel);
        }

        $evaluations = $query->get();

        // ============================================================
        // STATISTICS
        // ============================================================

        $stats = [
            'total_students' => $evaluations->pluck('student_id')->unique()->count(),
            'total_courses' => $evaluations->pluck('course_id')->unique()->count(),
            'low_risk' => $evaluations->where('risk_level', 'Low')->count(),
            'medium_risk' => $evaluations->where('risk_level', 'Medium')->count(),
            'high_risk' => $evaluations->where('risk_level', 'High')->count(),
            'eligible' => $evaluations->where('eligibility_status', 'Eligible')->count(),
            'warning' => $evaluations->where('eligibility_status', 'Warning')->count(),
            'not_eligible' => $evaluations->where('eligibility_status', 'Not Eligible')->count(),
            'recovering' => $evaluations->where('recovery_status', 'Recovering')->count(),
            'declining' => $evaluations->where('recovery_status', 'Declining')->count(),
            'critical' => $evaluations->where('recovery_status', 'Critical')->count(),
        ];

        // Risk counts for chart
        $riskCounts = [
            'Low' => $evaluations->where('risk_level', 'Low')->count(),
            'Medium' => $evaluations->where('risk_level', 'Medium')->count(),
            'High' => $evaluations->where('risk_level', 'High')->count(),
        ];

        // ============================================================
        // RISK FACTORS (from risk_factors JSON)
        // ============================================================

        $riskFactors = [];
        foreach ($evaluations as $eval) {
            // FIX: use the casted array directly, no json_decode needed
            $factors = $eval->risk_factors ?? [];
            foreach ($factors as $factor) {
                if (!isset($riskFactors[$factor])) {
                    $riskFactors[$factor] = 0;
                }
                $riskFactors[$factor]++;
            }
        }
        arsort($riskFactors); // sort by frequency

        // ============================================================
        // RISK TREND (last 6 months)
        // ============================================================

        $riskTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthEvaluations = AttendanceEvaluation::whereBetween('evaluation_date', [$monthStart, $monthEnd])
                ->get();

            $riskTrend[] = [
                'month' => $month->format('M'),
                'high_risk' => $monthEvaluations->where('risk_level', 'High')->count(),
                'medium_risk' => $monthEvaluations->where('risk_level', 'Medium')->count(),
                'low_risk' => $monthEvaluations->where('risk_level', 'Low')->count(),
            ];
        }

        // ============================================================
        // RISK DATA (for the table with roll call components)
        // ============================================================

        $riskData = [];
        // Get all students with evaluations (latest per student)
        $latestEval = AttendanceEvaluation::select('student_id', DB::raw('MAX(evaluation_date) as latest_date'))
            ->groupBy('student_id')
            ->pluck('latest_date', 'student_id');

        foreach ($latestEval as $studentId => $date) {
            $eval = AttendanceEvaluation::where('student_id', $studentId)
                ->where('evaluation_date', $date)
                ->first();

            if ($eval && ($eval->risk_level == 'Medium' || $eval->risk_level == 'High')) {
                $riskData[] = [
                    'student' => $eval->student,
                    'attendance' => $eval->attendance_percentage,
                    'roll_call' => $eval->roll_call_total ?? 0,
                    'consistency' => $eval->consistency_marks ?? 0,
                    'punctuality' => $eval->punctuality_marks ?? 0,
                    'participation' => $eval->participation_marks ?? 0,
                    'level' => $eval->risk_level,
                    'score' => $eval->risk_score,
                    // FIX: use the casted array directly
                    'factors' => $eval->risk_factors ?? [],
                ];
            }
        }

        // Sort by risk level (High first)
        usort($riskData, function($a, $b) {
            $order = ['High' => 0, 'Medium' => 1, 'Low' => 2];
            return $order[$a['level']] - $order[$b['level']];
        });

        // ============================================================
        // DEPARTMENT RISK DISTRIBUTION (for bars)
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
        // RETURN VIEW
        // ============================================================

        return view('admin.risk.index', compact(
            'departments',
            'stats',
            'riskCounts',
            'riskFactors',
            'riskTrend',
            'riskData',
            'riskByDepartment',
            'departmentId',
            'year',
            'riskLevel',
            'yearLabels'
        ));
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

        // Risk by course in department
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
