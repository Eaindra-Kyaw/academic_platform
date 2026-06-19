<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'current_year',
        'major_id',
        'department_id',
        'gpa',
        'total_credits',
        'status',
        'enrollment_date',
        'graduation_date',
        'profile_photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'graduation_date' => 'date',
        'gpa' => 'decimal:2',
        'current_year' => 'integer',
        'total_credits' => 'integer',
    ];

    /**
     * Get the user account associated with the student
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the major of the student
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get the department of the student
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the enrollments for the student
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the courses the student is enrolled in
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_student')
                    ->withPivot('enrolled_at', 'status', 'grade')
                    ->withTimestamps();
    }

    /**
     * Get the attendance records for the student
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the grades for the student
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Check if student is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Scope a query to only include active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by year
     */
    public function scopeYear($query, $year)
    {
        return $query->where('current_year', $year);
    }

    /**
     * Scope a query to filter by department
     */
    public function scopeDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
