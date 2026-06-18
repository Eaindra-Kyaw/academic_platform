<?php
// app/Models/Department.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Remove this: use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory; // Remove SoftDeletes

    protected $fillable = [
        'code',
        'name',
        'head_of_department',
        'description',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('role_id', 3);
    }

    public function lecturers()
    {
        return $this->hasMany(User::class)->where('role_id', 2);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Get students grouped by year
    public function studentsByYear()
    {
        return $this->students()
            ->selectRaw('current_year, count(*) as total, avg(attendance_percentage) as avg_attendance')
            ->leftJoin('enrollments', 'users.id', '=', 'enrollments.student_id')
            ->groupBy('current_year')
            ->orderBy('current_year')
            ->get()
            ->keyBy('current_year');
    }

    // Get courses grouped by year
    public function coursesByYear()
    {
        return $this->courses()
            ->with(['lecturer', 'students'])
            ->get()
            ->groupBy('year');
    }

    // Get all available years
    public function getAvailableYearsAttribute()
    {
        return $this->students()
            ->select('current_year')
            ->distinct()
            ->orderBy('current_year')
            ->pluck('current_year')
            ->toArray();
    }

    // Get overall attendance
    public function getOverallAttendanceAttribute()
    {
        return round($this->students()
            ->leftJoin('enrollments', 'users.id', '=', 'enrollments.student_id')
            ->avg('enrollments.attendance_percentage') ?? 0, 1);
    }
}
