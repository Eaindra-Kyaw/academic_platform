<?php
// app/Models/AssessmentSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSubmission extends Model
{
    use HasFactory;

    protected $table = 'assessment_submissions';

    protected $fillable = [
        'assessment_id',
        'student_id',
        'course_id',
        'lecturer_id',
        'answers',
        'comments',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'submitted_at' => 'datetime',
    ];

    // ✅ FIX: Specify the foreign key column
    public function assessment()
    {
        return $this->belongsTo(CourseAssessment::class, 'assessment_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function getAverageRatingAttribute()
    {
        $ratings = array_filter($this->answers, function($value) {
            return is_numeric($value);
        });
        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 2) : 0;
    }
}
