<?php
// app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

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

    // Year mapping for display
    public static $yearDisplayMap = [
        'First Year' => 'First Year',
        'Second Year' => 'Second Year',
        'Third Year' => 'Third Year',
        'Fourth Year' => 'Fourth Year',
        'Fifth Year' => 'Fifth Year',
        'Sixth Year' => 'Sixth Year',
    ];

    // Numeric to full year name mapping
    public static $yearNumericMap = [
        '1' => 'First Year',
        '2' => 'Second Year',
        '3' => 'Third Year',
        '4' => 'Fourth Year',
        '5' => 'Fifth Year',
        '6' => 'Sixth Year',
    ];

    // Full year name to numeric mapping
    public static $yearReverseMap = [
        'First Year' => '1',
        'Second Year' => '2',
        'Third Year' => '3',
        'Fourth Year' => '4',
        'Fifth Year' => '5',
        'Sixth Year' => '6',
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
            ->withPivot([
                'id',
                'attendance_percentage',
                'roll_call_mark',
                'eligibility_status',
                'status',
                'enrollment_date',
                'approved_at',
                'created_at',
                'updated_at'
            ])
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

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    // Get student count (approved only)
    public function getStudentCountAttribute()
    {
        return $this->students()->wherePivot('status', 'approved')->count();
    }

    // Get total enrollments count (all statuses)
    public function getTotalEnrollmentsAttribute()
    {
        return $this->enrollments()->count();
    }

    // Get pending enrollments count
    public function getPendingCountAttribute()
    {
        return $this->enrollments()->where('status', 'pending')->count();
    }

    // Get approved enrollments count
    public function getApprovedCountAttribute()
    {
        return $this->enrollments()->where('status', 'approved')->count();
    }

    // Get rejected enrollments count
    public function getRejectedCountAttribute()
    {
        return $this->enrollments()->where('status', 'rejected')->count();
    }

    // Get average attendance
    public function getAverageAttendanceAttribute()
    {
        return round($this->students()
            ->wherePivot('status', 'approved')
            ->avg('enrollments.attendance_percentage') ?? 0, 1);
    }

    // Get year label with proper formatting
    public function getYearLabelAttribute()
    {
        return $this->year ?? 'N/A';
    }

    // Get year numeric value
    public function getYearNumericAttribute()
    {
        return self::$yearReverseMap[$this->year] ?? null;
    }

    // Get semester label
    public function getSemesterLabelAttribute()
    {
        return $this->semester ?? 'N/A';
    }

    // Check if student is enrolled
    public function hasStudent($studentId)
    {
        return $this->students()->where('users.id', $studentId)->exists();
    }

    // Get enrollment status for a student
    public function getStudentEnrollmentStatus($studentId)
    {
        $enrollment = $this->enrollments()
            ->where('student_id', $studentId)
            ->first();

        return $enrollment ? $enrollment->status : null;
    }

    // Get all available years with course counts
    public static function getYearsWithCounts($departmentId = null)
    {
        $query = self::query();
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $years = $query->select('year', \DB::raw('count(*) as total'))
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->pluck('total', 'year')
            ->toArray();

        // Ensure all 6 years are present
        $allYears = ['First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'];
        $result = [];
        foreach ($allYears as $year) {
            $result[$year] = $years[$year] ?? 0;
        }

        return $result;
    }
}
