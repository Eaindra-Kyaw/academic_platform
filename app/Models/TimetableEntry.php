<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimetableEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'lecturer_id',
        'department_id',
        'academic_year',
        'semester',
        'year_level',
        'section',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'building',
        'session_type',
        'is_alternate_week',
        'alternate_week_type',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_alternate_week' => 'boolean',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Accessors
    public function getTimeRangeAttribute()
    {
        return $this->start_time->format('h:i A') . ' - ' . $this->end_time->format('h:i A');
    }

    public function getDurationAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    // Scopes
    public function scopeForLecturer($query, $lecturerId)
    {
        return $query->where('lecturer_id', $lecturerId);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
