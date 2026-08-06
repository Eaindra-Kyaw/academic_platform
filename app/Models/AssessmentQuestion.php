<?php
// app/Models/AssessmentQuestion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $table = 'assessment_questions';

    protected $fillable = [
        'assessment_id',
        'order',
        'question_text',
        'type',
        'min_rating',
        'max_rating',
    ];

    protected $casts = [
        'min_rating' => 'integer',
        'max_rating' => 'integer',
    ];

    // ✅ FIX: Specify the foreign key column
    public function assessment()
    {
        return $this->belongsTo(CourseAssessment::class, 'assessment_id');
    }
}
