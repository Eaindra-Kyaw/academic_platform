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
        'is_active',
    ];

    protected $casts = [
        'is_alternate_week' => 'boolean',
        'is_active' => 'boolean',
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
        return date('h:i A', strtotime($this->start_time)) . ' - ' .
               date('h:i A', strtotime($this->end_time));
    }

    public function getSessionTypeLabelAttribute()
    {
        $types = [
            'lecture' => '📚 Lecture',
            'tutorial' => '📝 Tutorial',
            'lab' => '🔬 Lab',
            'seminar' => '🎤 Seminar',
            'workshop' => '🛠️ Workshop',
            'other' => '📋 Other',
        ];
        return $types[$this->session_type] ?? $this->session_type;
    }

    // Scopes
    public function scopeForLecturer($query, $lecturerId)
    {
        return $query->where('lecturer_id', $lecturerId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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

    public function scopeBySessionType($query, $type)
    {
        return $query->where('session_type', $type);
    }
}
