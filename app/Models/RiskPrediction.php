<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'course_id', 'risk_score', 'risk_level',
        'consecutive_absences', 'attendance_trend', 'attendance_risk_points',
        'roll_call_risk_points', 'absence_risk_points', 'trend_risk_points',
        'risk_explanation', 'prediction_date'
    ];

    protected $casts = [
        'prediction_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
