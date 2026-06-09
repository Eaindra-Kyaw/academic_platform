<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lecturer_id',
        'session_date',
        'start_time',
        'end_time',
        'qr_code',
        'qr_expiry',
        'session_token',
        'is_active',
        'is_locked',
        'total_students',
        'present_count',
        'absent_count',
        'late_count'
    ];

    protected $casts = [
        'session_date' => 'date',
        'qr_expiry' => 'datetime',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }
}
