<?php
// app/Http/Controllers/Admin/RiskAnalysisController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Department;
use App\Models\Course;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
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
        // Get filters
        $departmentId = $request->input('department_id');
        $year = $request->input('year');
        $riskLevel = $request->input('risk_level');

        // Get all students
        $students = User::where('role_id', 3)
            ->with(['department', 'enrollments.course']);

        if ($departmentId) {
            $students->where('department_id', $departmentId);
        }

        if ($year) {
            $students->where('current_year', $year);
        }

        $students = $students->get();

        // Calculate risk for each student
        $riskData = [];
        $riskCounts = ['Low' => 0, 'Medium' => 0, 'High' => 0];
        $riskByDepartment = [];
        $riskByYear = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

        foreach ($students as $student) {
            $risk = $this->calculateStudentRisk($student);

            if ($risk['score'] > 0) {
                $riskData[] = $risk;
                $riskCounts[$risk['level']]++;

                // Department breakdown
                $deptName = $student->department->name ?? 'Unknown';
                if (!isset($riskByDepartment[$deptName])) {
                    $riskByDepartment[$deptName] = ['Low' => 0, 'Medium' => 0, 'High' => 0];
                }
                $riskByDepartment[$deptName][$risk['level']]++;

                // Year breakdown
                $yearKey = $student->current_year ?? 0;
                if ($yearKey >= 1 && $yearKey <= 6) {
                    $riskByYear[$yearKey]++;
                }
            }
        }

        // Sort risk data by score (highest first)
        usort($riskData, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // Apply risk level filter
        if ($riskLevel) {
            $riskData = array_filter($riskData, function($item) use ($riskLevel) {
                return $item['level'] === $riskLevel;
            });
        }

        // Get risk factors distribution
        $riskFactors = $this->getRiskFactorsDistribution($riskData);

        // Get statistics
        $stats = [
            'total_students' => $students->count(),
            'low_risk' => $riskCounts['Low'],
            'medium_risk' => $riskCounts['Medium'],
            'high_risk' => $riskCounts['High'],
            'risk_rate' => $students->count() > 0
                ? round((($riskCounts['Medium'] + $riskCounts['High']) / $students->count()) * 100, 1)
                : 0,
        ];

        // Get departments for filter
        $departments = Department::orderBy('name')->get();

        // Get year labels
        $yearLabels = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];

        // Risk trend (last 6 months)
        $riskTrend = $this->getRiskTrend();

        return view('admin.risk.index', compact(
            'riskData',
            'riskCounts',
            'stats',
            'riskByDepartment',
            'riskByYear',
            'riskFactors',
            'riskTrend',
            'departments',
            'departmentId',
            'year',
            'riskLevel',
            'yearLabels'
        ));
    }

    /**
     * Calculate risk for a single student
     */
    private function calculateStudentRisk($student)
    {
        // Get approved enrollments
        $enrollments = $student->enrollments()->where('status', 'approved')->get();

        if ($enrollments->isEmpty()) {
            return [
                'student' => $student,
                'score' => 0,
                'level' => 'Low',
                'factors' => [],
                'attendance' => 0,
                'roll_call' => 0,
                'consecutive_absences' => 0,
                'trend' => 'Stable',
            ];
        }

        // Calculate attendance percentage
        $avgAttendance = $enrollments->avg('attendance_percentage') ?? 0;
        $attendancePoints = $this->getAttendanceRiskPoints($avgAttendance);

        // Calculate roll call score
        $avgRollCall = $enrollments->avg('roll_call_mark') ?? 0;
        $rollCallPoints = $this->getRollCallRiskPoints($avgRollCall);

        // Calculate consecutive absences
        $consecutiveAbsences = $this->getConsecutiveAbsences($student->id);
        $consecutivePoints = $this->getConsecutiveAbsencePoints($consecutiveAbsences);

        // Calculate attendance trend
        $trend = $this->getAttendanceTrend($student->id);
        $trendPoints = $this->getTrendPoints($trend);

        // Calculate total risk score
        $riskScore = ($attendancePoints * 0.40) + ($rollCallPoints * 0.25) +
                     ($consecutivePoints * 0.20) + ($trendPoints * 0.15);

        $riskScore = round($riskScore);

        // Determine risk level
        $level = 'Low';
        if ($riskScore >= 70) {
            $level = 'High';
        } elseif ($riskScore >= 40) {
            $level = 'Medium';
        }

        // Build risk factors explanation
        $factors = [];
        if ($attendancePoints > 50) {
            $factors[] = 'Attendance below 70%';
        }
        if ($rollCallPoints > 50) {
            $factors[] = 'Roll call score below 5';
        }
        if ($consecutiveAbsences >= 3) {
            $factors[] = $consecutiveAbsences . ' consecutive absences';
        }
        if ($trend === 'Declining') {
            $factors[] = 'Attendance trend declining';
        }
        if ($trend === 'Slight Decline') {
            $factors[] = 'Attendance trend slightly declining';
        }

        return [
            'student' => $student,
            'score' => $riskScore,
            'level' => $level,
            'factors' => $factors,
            'attendance' => round($avgAttendance, 1),
            'roll_call' => round($avgRollCall, 1),
            'consecutive_absences' => $consecutiveAbsences,
            'trend' => $trend,
            'attendance_points' => $attendancePoints,
            'roll_call_points' => $rollCallPoints,
            'consecutive_points' => $consecutivePoints,
            'trend_points' => $trendPoints,
            'enrollments_count' => $enrollments->count(),
        ];
    }

    /**
     * Get attendance risk points
     */
    private function getAttendanceRiskPoints($attendance)
    {
        if ($attendance >= 90) return 0;
        if ($attendance >= 80) return 25;
        if ($attendance >= 70) return 50;
        if ($attendance >= 60) return 75;
        return 100;
    }

    /**
     * Get roll call risk points
     */
    private function getRollCallRiskPoints($rollCall)
    {
        if ($rollCall >= 9) return 0;
        if ($rollCall >= 7) return 25;
        if ($rollCall >= 5) return 50;
        if ($rollCall >= 3) return 75;
        return 100;
    }

    /**
     * Get consecutive absences
     */
    private function getConsecutiveAbsences($studentId)
    {
        // Get recent attendance sessions
        $sessions = AttendanceSession::where('status', 'ended')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $consecutive = 0;
        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('session_id', $session->id)
                ->where('student_id', $studentId)
                ->whereIn('status', ['absent'])
                ->first();

            if ($record) {
                $consecutive++;
            } else {
                break;
            }
        }

        return $consecutive;
    }

    /**
     * Get attendance trend
     */
    private function getAttendanceTrend($studentId)
    {
        // Get attendance records grouped by month
        $records = AttendanceRecord::where('student_id', $studentId)
            ->whereIn('status', ['present', 'late'])
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(3)
            ->get();

        if ($records->count() < 2) {
            return 'Stable';
        }

        $trends = [];
        $months = $records->pluck('count')->toArray();

        if (count($months) >= 2) {
            $diff = $months[0] - $months[1];
            $percentChange = $months[1] > 0 ? ($diff / $months[1]) * 100 : 0;

            if ($percentChange > 10) return 'Improving';
            if ($percentChange > 5) return 'Slight Improvement';
            if ($percentChange < -10) return 'Declining';
            if ($percentChange < -5) return 'Slight Decline';
        }

        return 'Stable';
    }

    /**
     * Get consecutive absence points
     */
    private function getConsecutiveAbsencePoints($absences)
    {
        if ($absences == 0) return 0;
        if ($absences == 1) return 25;
        if ($absences == 2) return 50;
        if ($absences == 3) return 75;
        return 100;
    }

    /**
     * Get trend points
     */
    private function getTrendPoints($trend)
    {
        return match($trend) {
            'Improving' => 0,
            'Slight Improvement' => 15,
            'Stable' => 25,
            'Slight Decline' => 50,
            'Declining' => 75,
            default => 25,
        };
    }

    /**
     * Get risk factors distribution
     */
    private function getRiskFactorsDistribution($riskData)
    {
        $factors = [
            'Attendance below 70%' => 0,
            'Roll call score below 5' => 0,
            '3+ consecutive absences' => 0,
            'Attendance trend declining' => 0,
            'Attendance trend slightly declining' => 0,
        ];

        foreach ($riskData as $data) {
            if ($data['level'] === 'Low') continue;

            foreach ($data['factors'] as $factor) {
                if (isset($factors[$factor])) {
                    $factors[$factor]++;
                }
            }
        }

        return $factors;
    }

    /**
     * Get risk trend over time
     */
    private function getRiskTrend()
    {
        $trend = [];
        $months = 6;

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');

            // Get students with risk score
            $students = User::where('role_id', 3)->get();
            $highRisk = 0;
            $mediumRisk = 0;
            $total = 0;

            foreach ($students as $student) {
                $enrollments = $student->enrollments()
                    ->where('status', 'approved')
                    ->where('created_at', '<=', $date)
                    ->get();

                if ($enrollments->isEmpty()) continue;

                $avgAttendance = $enrollments->avg('attendance_percentage') ?? 0;
                $riskScore = $this->getAttendanceRiskPoints($avgAttendance) * 0.40;
                // Simplified risk calculation

                if ($riskScore >= 70) $highRisk++;
                elseif ($riskScore >= 40) $mediumRisk++;
                $total++;
            }

            $trend[] = [
                'month' => $monthLabel,
                'high_risk' => $highRisk,
                'medium_risk' => $mediumRisk,
                'total' => $total,
            ];
        }

        return $trend;
    }

    /**
     * Export risk report
     */
    public function export(Request $request)
    {
        // Get students with risk data
        $students = User::where('role_id', 3)->get();
        $data = [];

        foreach ($students as $student) {
            $risk = $this->calculateStudentRisk($student);
            if ($risk['score'] > 0) {
                $data[] = [
                    'Student Name' => $student->name,
                    'Student ID' => $student->student_id ?? 'N/A',
                    'Department' => $student->department->name ?? 'N/A',
                    'Year' => $student->current_year ?? 'N/A',
                    'Attendance %' => $risk['attendance'] . '%',
                    'Roll Call Score' => $risk['roll_call'],
                    'Consecutive Absences' => $risk['consecutive_absences'],
                    'Trend' => $risk['trend'],
                    'Risk Score' => $risk['score'],
                    'Risk Level' => $risk['level'],
                    'Risk Factors' => implode('; ', $risk['factors']),
                ];
            }
        }

        // Sort by risk score (highest first)
        usort($data, function($a, $b) {
            return $b['Risk Score'] - $a['Risk Score'];
        });

        // Generate CSV
        $filename = 'risk_analysis_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
