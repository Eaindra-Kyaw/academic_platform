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
        'roll_call_mark',
        'eligibility_status',
        'consecutive_absences',
        'longest_absence_streak',
        'attendance_trend',
        'sessions_needed',
        'evaluation_week'
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
        'roll_call_mark' => 'decimal:2',
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
