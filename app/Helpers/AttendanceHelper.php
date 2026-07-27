<?php

namespace App\Helpers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class AttendanceHelper
{
    // ============================================================
    // ATTENDANCE PERCENTAGE – SESSION-BASED
    // ============================================================

    /**
     * Calculate attendance percentage from attended and total sessions.
     * Each class period = 1 session.
     */
    public static function calculateAttendance($attendedSessions, $totalSessions)
    {
        if ($totalSessions == 0) return 0;
        return round(($attendedSessions / $totalSessions) * 100, 1);
    }

    /**
     * Calculate session-based attendance for a student over a date range.
     * Each class period = 1 session.
     */
    public static function calculateAttendanceForPeriod($studentId, $courseId, $startDate, $endDate)
    {
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->where('is_cancelled', false)
            ->whereBetween('session_date', [$startDate, $endDate])
            ->get();

        // Count SESSIONS (each class period = 1 session)
        $totalSessions = $sessions->count();
        if ($totalSessions == 0) {
            return ['percentage' => 0, 'attended' => 0, 'total' => 0];
        }

        $attendedSessions = 0;
        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('student_id', $studentId)
                ->where('attendance_session_id', $session->id)
                ->whereIn('status', ['present', 'late'])
                ->first();
            if ($record) {
                $attendedSessions++;
            }
        }

        $percentage = round(($attendedSessions / $totalSessions) * 100, 1);
        return [
            'percentage' => $percentage,
            'attended'   => $attendedSessions,
            'total'      => $totalSessions,
        ];
    }

    // ============================================================
    // ELIGIBILITY (KG+12)
    // ============================================================

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

    // ============================================================
    // KG+12 ROLL CALL (max 10)
    // ============================================================

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

    // ============================================================
    // RISK SCORE – IMPROVED MULTI-FACTOR VERSION
    // ============================================================

    /**
     * Calculate a comprehensive risk score using multiple factors:
     * - Attendance percentage (40% weight)
     * - Roll call total (25% weight)
     * - Consecutive absences (20% weight)
     * - Attendance trend (15% weight)
     */
    public static function calculateRiskScore($attendancePercentage, $rollCallTotal = null, $consecutiveAbsences = null, $trend = null)
    {
        // Attendance risk (0–100)
        if ($attendancePercentage >= 75) {
            $attRisk = 20;
        } elseif ($attendancePercentage >= 60) {
            $attRisk = 50;
        } elseif ($attendancePercentage >= 40) {
            $attRisk = 75;
        } else {
            $attRisk = 90;
        }

        // Roll call risk (0–100)
        $rollRisk = 50; // default if null
        if ($rollCallTotal !== null) {
            if ($rollCallTotal >= 8) $rollRisk = 20;
            elseif ($rollCallTotal >= 6) $rollRisk = 40;
            elseif ($rollCallTotal >= 4) $rollRisk = 60;
            else $rollRisk = 80;
        }

        // Consecutive absences risk (0–100)
        $absRisk = 0;
        if ($consecutiveAbsences !== null) {
            if ($consecutiveAbsences >= 4) $absRisk = 90;
            elseif ($consecutiveAbsences >= 2) $absRisk = 60;
            elseif ($consecutiveAbsences >= 1) $absRisk = 30;
        }

        // Trend risk (0–100)
        $trendRisk = 50;
        if ($trend !== null) {
            if ($trend == 'improving') $trendRisk = 20;
            elseif ($trend == 'stable') $trendRisk = 50;
            elseif ($trend == 'declining') $trendRisk = 75;
            elseif ($trend == 'critical') $trendRisk = 90;
        }

        // Weighted average
        $riskScore = ($attRisk * 0.40) + ($rollRisk * 0.25) + ($absRisk * 0.20) + ($trendRisk * 0.15);
        return round($riskScore);
    }

    /**
     * Get risk level from attendance only (legacy – kept for backward compatibility)
     */
    public static function getRiskLevelFromAttendance($attendancePercentage)
    {
        if ($attendancePercentage >= 75) {
            return 'Low';
        } elseif ($attendancePercentage >= 60) {
            return 'Medium';
        } else {
            return 'High';
        }
    }

    public static function getRiskLevel($riskScore)
    {
        if ($riskScore <= 35) return 'Low';
        if ($riskScore <= 65) return 'Medium';
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
            $explanations[] = '❌ Attendance critically low (' . $attendancePercentage . '%)';
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

    // ============================================================
    // CONSECUTIVE ABSENCES & TREND
    // ============================================================

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
