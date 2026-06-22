<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RollCallSubmission extends Model
{
    protected $fillable = [
        'course_id',
        'lecturer_id',
        'student_id',
        'month',
        'year',
        'total_conducted_periods',
        'total_attended_periods',
        'attendance_percentage',
        'roll_call_mark',
        'eligibility_status',
        'submission_status',
        'lecturer_notes',
        'admin_notes',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopePending($query)
    {
        return $query->where('submission_status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('submission_status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('submission_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('submission_status', 'rejected');
    }
}
