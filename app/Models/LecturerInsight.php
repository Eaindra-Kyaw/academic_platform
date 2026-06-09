<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecturer_id',
        'course_id',
        'insight_type',
        'insight_text',
        'insight_data',
        'is_dismissed'
    ];

    protected $casts = [
        'insight_data' => 'array',
        'is_dismissed' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
