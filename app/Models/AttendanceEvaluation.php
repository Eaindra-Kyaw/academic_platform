<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEvaluation extends Model
{
    use HasFactory;

   protected $fillable = [
    'student_id',
    'course_id',
    'total_sessions',
    'attended_sessions',
    'attendance_percentage',
    'consistency_marks',
    'punctuality_marks',
    'participation_marks',
    'roll_call_total',
    'eligibility_status',
    'consecutive_absences',
    'attendance_trend',
    'risk_score',
    'risk_level',
    'risk_factors',
    'evaluated_at',
];

protected $casts = [
    'risk_factors' => 'array',
    'evaluated_at' => 'datetime',
];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scopes
    public function scopeEligible($query)
    {
        return $query->where('eligibility_status', 'Eligible');
    }

    public function scopeAtRisk($query)
    {
        return $query->whereIn('risk_level', ['Medium', 'High']);
    }

    public function scopeRecovering($query)
    {
        return $query->where('recovery_status', 'Recovering');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('evaluation_date', 'desc');
    }

    // Accessors
    public function getEligibilityColorAttribute()
    {
        return match($this->eligibility_status) {
            'Eligible' => 'green',
            'Warning' => 'yellow',
            'Not Eligible' => 'red',
            default => 'gray',
        };
    }

    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'Low' => 'green',
            'Medium' => 'yellow',
            'High' => 'red',
            default => 'gray',
        };
    }

    public function getHealthStatusAttribute()
    {
        $score = $this->academic_health_score;
        if ($score >= 90) return 'Excellent';
        if ($score >= 75) return 'Stable';
        if ($score >= 50) return 'At Risk';
        return 'Critical';
    }

    // Helper Methods
    public static function calculateRollCallScore($percentage)
    {
        return match(true) {
            $percentage >= 95 => 10,
            $percentage >= 90 => 9,
            $percentage >= 85 => 8,
            $percentage >= 80 => 7,
            $percentage >= 75 => 6,
            $percentage >= 70 => 5,
            $percentage >= 65 => 4,
            $percentage >= 60 => 3,
            $percentage >= 55 => 2,
            default => 1,
        };
    }

    public static function determineEligibility($percentage)
    {
        if ($percentage >= 75) return 'Eligible';
        if ($percentage >= 60) return 'Warning';
        return 'Not Eligible';
    }

    public static function determineRecoveryStatus($current, $previous)
    {
        if ($current > $previous) return 'Recovering';
        if ($current < $previous) return 'Declining';
        return 'Stable';
    }

    public static function calculateRiskScore($attendancePercentage, $rollCallScore, $consecutiveAbsences, $trend)
    {
        // Risk factors with weights
        $attendanceRisk = $attendancePercentage < 75 ? 75 : ($attendancePercentage < 85 ? 50 : 25);
        $rollCallRisk = $rollCallScore < 5 ? 75 : ($rollCallScore < 7 ? 50 : 25);
        $absenceRisk = $consecutiveAbsences >= 3 ? 75 : ($consecutiveAbsences >= 1 ? 50 : 25);
        $trendRisk = $trend == 'Declining' ? 75 : ($trend == 'Stable' ? 50 : 25);

        return round(($attendanceRisk * 0.40) + ($rollCallRisk * 0.25) + ($absenceRisk * 0.20) + ($trendRisk * 0.15));
    }

    public static function determineRiskLevel($score)
    {
        if ($score < 40) return 'Low';
        if ($score < 70) return 'Medium';
        return 'High';
    }
}
