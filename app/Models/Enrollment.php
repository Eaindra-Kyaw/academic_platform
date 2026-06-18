<?php
// app/Models/Enrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'enrollment_date',
        'status',
        'attendance_percentage',
        'roll_call_mark',
        'eligibility_status',
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
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

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DROPPED = 'dropped';

    const ELIGIBILITY_ELIGIBLE = 'eligible';
    const ELIGIBILITY_WARNING = 'warning';
    const ELIGIBILITY_NOT_ELIGIBLE = 'not_eligible';

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeEligible($query)
    {
        return $query->where('eligibility_status', self::ELIGIBILITY_ELIGIBLE);
    }

    // Get eligibility label with color
    public function getEligibilityLabelAttribute()
    {
        return match($this->eligibility_status) {
            self::ELIGIBILITY_ELIGIBLE => ['label' => 'Eligible', 'class' => 'success'],
            self::ELIGIBILITY_WARNING => ['label' => 'Warning', 'class' => 'warning'],
            self::ELIGIBILITY_NOT_ELIGIBLE => ['label' => 'Not Eligible', 'class' => 'danger'],
            default => ['label' => 'Unknown', 'class' => 'secondary'],
        };
    }
}
