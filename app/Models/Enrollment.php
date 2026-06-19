<?php
// app/Models/Enrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'approved_at',
        'dropped_at',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'approved_at' => 'datetime',
        'dropped_at' => 'datetime',
        'rejected_at' => 'datetime',
        'attendance_percentage' => 'decimal:2',
        'roll_call_mark' => 'decimal:2',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }

    public function scopeEligible($query)
    {
        return $query->where('eligibility_status', 'eligible');
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereHas('student', function($q) use ($year) {
            $q->where('current_year', $year);
        });
    }

    // ============================================================
    // ATTRIBUTES
    // ============================================================

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'status-pending',
            'approved' => 'status-approved',
            'rejected' => 'status-rejected',
            'dropped' => 'status-dropped',
            default => '',
        };
    }

    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'pending' => 'bi-clock-history',
            'approved' => 'bi-check-circle',
            'rejected' => 'bi-x-circle',
            'dropped' => 'bi-dash-circle',
            default => '',
        };
    }

    public function getEligibilityBadgeClassAttribute()
    {
        return match($this->eligibility_status) {
            'eligible' => 'badge-eligible',
            'warning' => 'badge-warning',
            'not_eligible' => 'badge-not-eligible',
            default => '',
        };
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isDropped()
    {
        return $this->status === 'dropped';
    }

    public function canBeProcessed()
    {
        return $this->isPending();
    }

    public function approve()
    {
        if (!$this->canBeProcessed()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
        ]);

        return true;
    }

    public function reject($reason)
    {
        if (!$this->canBeProcessed()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => Carbon::now(),
        ]);

        return true;
    }

    public function drop()
    {
        if (!$this->isApproved()) {
            return false;
        }

        $this->update([
            'status' => 'dropped',
            'dropped_at' => Carbon::now(),
        ]);

        return true;
    }
}
