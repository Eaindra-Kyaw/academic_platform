<?php
// app/Models/CourseAssessment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssessment extends Model
{
    use HasFactory;

    protected $table = 'course_assessments';

    protected $fillable = [
        'name',
        'description',
        'semester_id',
        'year',
        'semester',
        'course_id',
        'lecturer_id',
        'status',
        'opens_at',
        'closes_at',
        'results_sent_at',
    ];

    protected $casts = [
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
        'results_sent_at' => 'datetime',
    ];

    // Relationships with explicit foreign keys
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    // ✅ FIX: Specify the foreign key column
    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'assessment_id');
    }

    // ✅ FIX: Specify the foreign key column
    public function submissions()
    {
        return $this->hasMany(AssessmentSubmission::class, 'assessment_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('opens_at')->orWhere('opens_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('closes_at')->orWhere('closes_at', '>=', now());
            });
    }

    public function scopeForYearSemester($query, $year, $semester)
    {
        return $query->where('year', $year)->where('semester', $semester);
    }

    // Helper methods
    public function isOpen()
    {
        if ($this->status !== 'active') return false;
        if ($this->opens_at && $this->opens_at > now()) return false;
        if ($this->closes_at && $this->closes_at < now()) return false;
        return true;
    }

    public function getSubmissionCountAttribute()
    {
        return $this->submissions()->count();
    }

    public function getStudentCountAttribute()
    {
        return $this->submissions()->distinct('student_id')->count('student_id');
    }

    public function getAverageRatingAttribute()
    {
        $allRatings = [];
        foreach ($this->submissions as $submission) {
            $answers = $submission->answers;
            foreach ($answers as $questionId => $rating) {
                if (is_numeric($rating)) {
                    $allRatings[] = $rating;
                }
            }
        }
        return count($allRatings) > 0 ? round(array_sum($allRatings) / count($allRatings), 2) : 0;
    }

    public function getResponseRateAttribute()
    {
        $total = $this->getTotalEnrolledStudents();
        return $total > 0 ? round(($this->student_count / $total) * 100, 1) : 0;
    }

    private function getTotalEnrolledStudents()
    {
        try {
            $query = Enrollment::where('status', 'approved');

            if ($this->course_id) {
                $query->where('course_id', $this->course_id);
            } else if ($this->year && $this->semester) {
                $query->whereHas('course', function($q) {
                    $q->where('year', $this->year)
                      ->where('semester', $this->semester);
                });
            }

            $count = $query->distinct('student_id')->count('student_id');
            return $count > 0 ? $count : 1;
        } catch (\Exception $e) {
            return 1;
        }
    }
}
