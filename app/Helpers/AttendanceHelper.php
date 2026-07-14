<?php

namespace App\Helpers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class AttendanceHelper
{
    // Attendance percentage
    public static function calculateAttendance($attendedSessions, $totalSessions)
    {
        if ($totalSessions == 0) return 0;
        return round(($attendedSessions / $totalSessions) * 100, 1);
    }

    // Eligibility (KG+12)
    public static function getEligibilityStatus($attendancePercentage)
    {
        if ($attendancePercentage >= 75) return 'eligible';
        if ($attendancePercentage >= 60) return 'warning';
        return 'not_eligible';
    }

    public static function getEligibilityLabel($status)
    {
        return match($status) {
            'eligible'     => ['label' => 'Eligible', 'class' => 'success', 'icon' => '✅'],
            'warning'      => ['label' => 'Warning', 'class' => 'warning', 'icon' => '⚠️'],
            'not_eligible' => ['label' => 'Not Eligible', 'class' => 'danger', 'icon' => '❌'],
            default        => ['label' => 'Unknown', 'class' => 'secondary', 'icon' => '❓'],
        };
    }

    // KG+12 Roll Call (max 10)
    public static function calculateRollCallMark($attendancePercentage, $lateCount = 0, $participationMark = 1.5)
    {
        // Consistency (max 6)
        $consistency = 0.5;
        if ($attendancePercentage >= 95) $consistency = 6.0;
        elseif ($attendancePercentage >= 90) $consistency = 5.0;
        elseif ($attendancePercentage >= 85) $consistency = 4.0;
        elseif ($attendancePercentage >= 80) $consistency = 3.0;
        elseif ($attendancePercentage >= 75) $consistency = 2.0;
        elseif ($attendancePercentage >= 70) $consistency = 1.5;
        elseif ($attendancePercentage >= 60) $consistency = 1.0;

        // Punctuality (max 2)
        $punctuality = 0.5;
        if ($lateCount == 0) $punctuality = 2.0;
        elseif ($lateCount <= 2) $punctuality = 1.5;
        elseif ($lateCount <= 5) $punctuality = 1.0;

        $participation = min(max($participationMark, 0.5), 2.0);

        $total = round($consistency + $punctuality + $participation, 1);

        return [
            'consistency'   => $consistency,
            'punctuality'   => $punctuality,
            'participation' => $participation,
            'total'         => $total,
        ];
    }

    // Risk Score (four factors)
    public static function attendanceToRiskPoints($attendancePercentage)
    {
        if ($attendancePercentage >= 90) return 0;
        if ($attendancePercentage >= 80) return 25;
        if ($attendancePercentage >= 75) return 39;
        if ($attendancePercentage >= 70) return 50;
        if ($attendancePercentage >= 65) return 60;
        if ($attendancePercentage >= 60) return 69;
        if ($attendancePercentage >= 55) return 80;
        if ($attendancePercentage >= 50) return 90;
        return 100;
    }

    public static function rollCallToRiskPoints($rollCallTotal)
    {
        if ($rollCallTotal >= 9) return 0;
        if ($rollCallTotal >= 7) return 25;
        if ($rollCallTotal >= 5) return 50;
        if ($rollCallTotal >= 3) return 75;
        return 100;
    }

    public static function consecutiveAbsencesToRiskPoints($consecutiveAbsences)
    {
        if ($consecutiveAbsences == 0) return 0;
        if ($consecutiveAbsences <= 2) return 25;
        if ($consecutiveAbsences <= 4) return 75;
        return 100;
    }

    public static function trendToRiskPoints($trend)
    {
        return match($trend) {
            'improving' => 0,
            'stable'    => 25,
            'declining' => 75,
            'critical'  => 100,
            default     => 25,
        };
    }

    public static function calculateRiskScore($attendancePercentage, $rollCallTotal, $consecutiveAbsences, $trend)
    {
        $attRisk = self::attendanceToRiskPoints($attendancePercentage);
        $rollRisk = self::rollCallToRiskPoints($rollCallTotal);
        $absRisk = self::consecutiveAbsencesToRiskPoints($consecutiveAbsences);
        $trendRisk = self::trendToRiskPoints($trend);

        return round(
            ($attRisk * 0.40) +
            ($rollRisk * 0.25) +
            ($absRisk * 0.20) +
            ($trendRisk * 0.15)
        );
    }

    public static function getRiskLevel($riskScore)
    {
        if ($riskScore <= 39) return 'Low';
        if ($riskScore <= 69) return 'Medium';
        return 'High';
    }

    public static function getRiskColor($riskLevel)
    {
        return match($riskLevel) {
            'Low'    => '#10b981',
            'Medium' => '#f59e0b',
            'High'   => '#ef4444',
            default  => '#6b7280',
        };
    }

    public static function getRiskBadgeClass($riskLevel)
    {
        return match($riskLevel) {
            'Low'    => 'success',
            'Medium' => 'warning',
            'High'   => 'danger',
            default  => 'secondary',
        };
    }

    public static function getRiskExplanation($attendancePercentage, $rollCallTotal, $consecutiveAbsences, $trend, $riskLevel)
    {
        $explanations = [];

        if ($attendancePercentage >= 75) {
            $explanations[] = '✅ Attendance above 75% threshold';
        } elseif ($attendancePercentage >= 60) {
            $explanations[] = '⚠️ Attendance below 75% (needs improvement)';
        } else {
            $explanations[] = '❌ Attendance critically low';
        }

        if ($rollCallTotal >= 7) {
            $explanations[] = "✅ Roll call score {$rollCallTotal}/10 (good)";
        } elseif ($rollCallTotal >= 5) {
            $explanations[] = "⚠️ Roll call score {$rollCallTotal}/10 (moderate)";
        } else {
            $explanations[] = "❌ Roll call score {$rollCallTotal}/10 (poor)";
        }

        if ($consecutiveAbsences == 0) {
            $explanations[] = '✅ No consecutive absences';
        } elseif ($consecutiveAbsences <= 2) {
            $explanations[] = "⚠️ {$consecutiveAbsences} consecutive absences detected";
        } else {
            $explanations[] = "❌ {$consecutiveAbsences} consecutive absences – urgent";
        }

        if ($trend == 'improving') {
            $explanations[] = '📈 Attendance trend improving';
        } elseif ($trend == 'stable') {
            $explanations[] = '📊 Attendance trend stable';
        } elseif ($trend == 'declining') {
            $explanations[] = '📉 Attendance trend declining';
        } else {
            $explanations[] = '🚨 Critical decline trend';
        }

        if ($riskLevel == 'Low') {
            $explanations[] = '🟢 Low risk – student on track';
        } elseif ($riskLevel == 'Medium') {
            $explanations[] = '🟡 Medium risk – intervention advised';
        } else {
            $explanations[] = '🔴 High risk – immediate intervention required';
        }

        return $explanations;
    }

    public static function getConsecutiveAbsences($studentId, $courseId)
    {
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->orderBy('session_date', 'desc')
            ->get();

        $consecutive = 0;
        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('student_id', $studentId)
                ->where('attendance_session_id', $session->id)
                ->first();

            if (!$record || $record->status === 'absent') {
                $consecutive++;
            } else {
                break;
            }
        }

        return $consecutive;
    }

    public static function getAttendanceTrend($studentId, $courseId)
    {
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->orderBy('session_date', 'asc')
            ->take(5)
            ->get();

        if ($sessions->count() < 3) {
            return 'stable';
        }

        $statuses = [];
        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('student_id', $studentId)
                ->where('attendance_session_id', $session->id)
                ->first();

            $statuses[] = ($record && $record->status !== 'absent') ? 1 : 0;
        }

        $len = count($statuses);
        if ($len < 4) return 'stable';

        $earlier = array_slice($statuses, 0, 2);
        $recent = array_slice($statuses, -2);

        $earlierAvg = array_sum($earlier) / count($earlier);
        $recentAvg = array_sum($recent) / count($recent);

        $diff = $recentAvg - $earlierAvg;

        if ($diff > 0.2) return 'improving';
        if ($diff < -0.2) return 'declining';
        if ($recentAvg == 0) return 'critical';
        return 'stable';
    }
}
