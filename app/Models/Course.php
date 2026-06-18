<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'department_id',
        'lecturer_id',
        'lecturer_name',
        'credits',
        'year',
        'semester',
        'academic_year',
        'room',
        'schedule_day',
        'schedule_time',
        'schedule_end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->withPivot(['attendance_percentage', 'roll_call_mark', 'eligibility_status', 'status'])
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function attendanceRecords()
    {
        return $this->hasManyThrough(AttendanceRecord::class, AttendanceSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Get student count
    public function getStudentCountAttribute()
    {
        return $this->students()->wherePivot('status', 'approved')->count();
    }

    // Get average attendance
    public function getAverageAttendanceAttribute()
    {
        return round($this->students()
            ->wherePivot('status', 'approved')
            ->avg('enrollments.attendance_percentage') ?? 0, 1);
    }

    // Get year label
    public function getYearLabelAttribute()
    {
        return $this->year ?? 'N/A';
    }

    // Get semester label
    public function getSemesterLabelAttribute()
    {
        return $this->semester ?? 'N/A';
    }
}
