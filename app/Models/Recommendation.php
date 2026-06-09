<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'recommendation_type',
        'recommendation_text',
        'priority',
        'is_read',
        'is_actioned',
        'generated_at',
        'expires_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_actioned' => 'boolean',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
