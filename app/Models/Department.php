<?php
// app/Models/Department.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'head_of_department',
        'description',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Users belonging to this department
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Students in this department (role_id = 3)
     */
    public function students()
    {
        return $this->hasMany(User::class)->where('role_id', 3);
    }

    /**
     * Lecturers in this department (role_id = 2)
     */
    public function lecturers()
    {
        return $this->hasMany(User::class)->where('role_id', 2);
    }

    /**
     * Courses offered by this department
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * All enrollments through courses in this department
     */
    public function enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, Course::class);
    }

    // ============================================================
    // ATTRIBUTE ACCESSORS (for use in views)
    // ============================================================

    /**
     * Get total enrollments count for this department
     */
    public function getEnrollmentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    /**
     * Get pending enrollments count for this department
     */
    public function getPendingEnrollmentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'pending')->count();
    }

    /**
     * Get approved enrollments count for this department
     */
    public function getApprovedEnrollmentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'approved')->count();
    }

    /**
     * Get rejected enrollments count for this department
     */
    public function getRejectedEnrollmentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'rejected')->count();
    }

    /**
     * Get dropped enrollments count for this department
     */
    public function getDroppedEnrollmentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'dropped')->count();
    }

    // ============================================================
    // YEAR-BASED METHODS
    // ============================================================

    /**
     * Get students grouped by year with statistics
     */
    public function studentsByYear()
    {
        return $this->students()
            ->selectRaw('
                current_year,
                count(*) as total,
                avg(attendance_percentage) as avg_attendance,
                avg(gpa) as avg_gpa
            ')
            ->leftJoin('enrollments', 'users.id', '=', 'enrollments.student_id')
            ->groupBy('current_year')
            ->orderBy('current_year')
            ->get()
            ->keyBy('current_year');
    }

    /**
     * Get courses grouped by year
     */
    public function coursesByYear()
    {
        return $this->courses()
            ->with(['lecturer', 'students'])
            ->get()
            ->groupBy('year');
    }

    /**
     * Get all available years for this department
     */
    public function getAvailableYearsAttribute()
    {
        return $this->students()
            ->select('current_year')
            ->distinct()
            ->orderBy('current_year')
            ->pluck('current_year')
            ->toArray();
    }

    /**
     * Get enrollment counts by year for this department
     */
    public function getEnrollmentCountsByYear()
    {
        $counts = [];
        for ($year = 1; $year <= 6; $year++) {
            $counts[$year] = $this->enrollments()
                ->whereHas('student', function($query) use ($year) {
                    $query->where('current_year', $year);
                })
                ->count();
        }
        return $counts;
    }

    /**
     * Get pending enrollment counts by year
     */
    public function getPendingCountsByYear()
    {
        $counts = [];
        for ($year = 1; $year <= 6; $year++) {
            $counts[$year] = $this->enrollments()
                ->where('status', 'pending')
                ->whereHas('student', function($query) use ($year) {
                    $query->where('current_year', $year);
                })
                ->count();
        }
        return $counts;
    }

    // ============================================================
    // ATTENDANCE & STATISTICS
    // ============================================================

    /**
     * Get overall attendance percentage for this department
     */
    public function getOverallAttendanceAttribute()
    {
        return round(
            $this->students()
                ->leftJoin('enrollments', 'users.id', '=', 'enrollments.student_id')
                ->avg('enrollments.attendance_percentage') ?? 0,
            1
        );
    }

    /**
     * Get total students count
     */
    public function getTotalStudentsAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Get total courses count
     */
    public function getTotalCoursesAttribute()
    {
        return $this->courses()->count();
    }

    /**
     * Get total lecturers count
     */
    public function getTotalLecturersAttribute()
    {
        return $this->lecturers()->count();
    }

    // ============================================================
    // HELPER METHODS FOR CONTROLLERS
    // ============================================================

    /**
     * Get enrollments for this department with optional filters
     */
    public function getEnrollments($year = null, $status = null)
    {
        $query = $this->enrollments()->with(['student', 'course']);

        if ($year !== null) {
            $query->whereHas('student', function($q) use ($year) {
                $q->where('current_year', $year);
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get course-wise enrollment counts
     */
    public function getCourseEnrollmentCounts()
    {
        return $this->courses()
            ->withCount(['enrollments as total_enrollments'])
            ->withCount(['enrollments as pending_enrollments' => function($query) {
                $query->where('status', 'pending');
            }])
            ->withCount(['enrollments as approved_enrollments' => function($query) {
                $query->where('status', 'approved');
            }])
            ->get();
    }

    /**
     * Get enrollment stats for dashboard
     */
    public function getEnrollmentStats()
    {
        return [
            'total' => $this->enrollments()->count(),
            'pending' => $this->enrollments()->where('status', 'pending')->count(),
            'approved' => $this->enrollments()->where('status', 'approved')->count(),
            'rejected' => $this->enrollments()->where('status', 'rejected')->count(),
            'dropped' => $this->enrollments()->where('status', 'dropped')->count(),
        ];
    }

    /**
     * Check if department has any enrollments
     */
    public function hasEnrollments()
    {
        return $this->enrollments()->exists();
    }

    /**
     * Get students eligible for enrollment (based on eligibility_status)
     */
    public function getEligibleStudents()
    {
        return $this->students()
            ->whereHas('enrollments', function($query) {
                $query->where('eligibility_status', 'eligible');
            })
            ->get();
    }

    /**
     * Get students with warning status
     */
    public function getWarningStudents()
    {
        return $this->students()
            ->whereHas('enrollments', function($query) {
                $query->where('eligibility_status', 'warning');
            })
            ->get();
    }

    // ============================================================
    // SEARCH / FILTER METHODS
    // ============================================================

    /**
     * Search students in department
     */
    public function searchStudents($searchTerm)
    {
        return $this->students()
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('student_id', 'LIKE', "%{$searchTerm}%");
            })
            ->get();
    }

    /**
     * Get courses with their enrollment status for a specific student
     */
    public function getCoursesWithStudentEnrollment($studentId)
    {
        return $this->courses()
            ->with(['enrollments' => function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }])
            ->get()
            ->map(function($course) {
                $course->student_enrolled = $course->enrollments->isNotEmpty();
                $course->enrollment_status = $course->enrollments->first()->status ?? null;
                return $course;
            });
    }
}
