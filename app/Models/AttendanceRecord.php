<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'remarks',
        'marked_by',
        'marked_at'
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    /**
     * Get the attendance session that this record belongs to
     */
    public function attendanceSession()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    /**
     * Get the student (user) associated with this attendance record
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user who marked this attendance
     */
    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Alias for attendanceSession() to maintain compatibility
     * This fixes the 'session' method not found error
     */
    public function session()
    {
        return $this->attendanceSession();
    }
}
