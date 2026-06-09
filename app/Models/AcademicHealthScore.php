<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicHealthScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_percentage_score',
        'roll_call_score',
        'attendance_streak_score',
        'engagement_trend_score',
        'academic_health_score',
        'health_category',
        'current_streak',
        'longest_streak',
        'recovery_status',
        'calculation_week'
    ];

    protected $casts = [
        'attendance_percentage_score' => 'integer',
        'roll_call_score' => 'integer',
        'attendance_streak_score' => 'integer',
        'engagement_trend_score' => 'integer',
        'academic_health_score' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
