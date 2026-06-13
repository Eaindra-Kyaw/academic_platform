<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance_sessions';

    protected $fillable = [
        'course_id',
        'lecturer_id',
        'session_token',
        'manual_code',
        'room',
        'duration',
        'status',
        'started_at',
        'expires_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the course that owns the attendance session
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the lecturer who created the session
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get all attendance records for this session
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'attendance_session_id');
    }

    /**
     * Get students marked present in this session
     */
    public function presentStudents()
    {
        return $this->belongsToMany(User::class, 'attendance_records', 'attendance_session_id', 'student_id')
                    ->wherePivot('status', 'present')
                    ->withPivot('status', 'scanned_at');
    }

    /**
     * Get students marked late in this session
     */
    public function lateStudents()
    {
        return $this->belongsToMany(User::class, 'attendance_records', 'attendance_session_id', 'student_id')
                    ->wherePivot('status', 'late')
                    ->withPivot('status', 'scanned_at');
    }

    /**
     * Check if session is active
     */
    public function isActive()
    {
        return $this->status === 'active' && (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Check if session is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get attendance percentage for a specific student
     */
    public function getStudentAttendance($studentId)
    {
        return $this->attendanceRecords()
                    ->where('student_id', $studentId)
                    ->first();
    }

    /**
     * Count total attendance records
     */
    public function getTotalAttendanceCount()
    {
        return $this->attendanceRecords()->count();
    }

    /**
     * Count present students (including late)
     */
    public function getPresentCount()
    {
        return $this->attendanceRecords()
                    ->whereIn('status', ['present', 'late'])
                    ->count();
    }

    /**
     * Count late students
     */
    public function getLateCount()
    {
        return $this->attendanceRecords()
                    ->where('status', 'late')
                    ->count();
    }

    /**
     * Count absent students
     */
    public function getAbsentCount()
    {
        $totalEnrolled = $this->course->enrollments()
                                     ->where('status', 'approved')
                                     ->count();
        return $totalEnrolled - $this->getPresentCount();
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    /**
     * Scope for ended sessions
     */
    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    /**
     * Scope for expired sessions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                     ->where('status', 'active');
    }
}
