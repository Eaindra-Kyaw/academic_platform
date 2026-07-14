<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
        'student_id',
        'current_year',
        'enrollment_year',
        'is_active',
        'must_change_password',
        'email_verified_at',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
            ->withPivot(['attendance_percentage', 'roll_call_mark', 'eligibility_status', 'status'])
            ->withTimestamps();
    }

    // For lecturers - courses they teach
    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    // For lecturers - students they teach
    public function taughtStudents()
    {
        return $this->hasManyThrough(
            User::class,
            Course::class,
            'lecturer_id',
            'id',
            'id',
            'id'
        );
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role_id', 3);
    }

    public function scopeLecturers($query)
    {
        return $query->where('role_id', 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Role checks
    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isLecturer()
    {
        return $this->role_id === 2;
    }

    public function isStudent()
    {
        return $this->role_id === 3;
    }

    // Helper methods
    public function attendancePercentage($courseId)
    {
        $enrollment = $this->enrollments()
            ->where('course_id', $courseId)
            ->first();

        return $enrollment ? $enrollment->attendance_percentage : 0;
    }

    public function rollCallScore($courseId)
    {
        $enrollment = $this->enrollments()
            ->where('course_id', $courseId)
            ->first();

        return $enrollment ? $enrollment->roll_call_mark : 0;
    }

    public function getYearLabelAttribute()
    {
        if (!$this->current_year) return 'N/A';

        $suffixes = ['th', 'st', 'nd', 'rd'];
        $suffix = $this->current_year <= 3 ? $suffixes[$this->current_year] : 'th';
        return $this->current_year . $suffix . ' Year';
    }

    public function getFormattedStudentIdAttribute()
    {
        return $this->student_id ?? 'N/A';
    }
}
