<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'total_students',
        'total_lecturers',
        'total_courses',
        'attendance_rate',
        'students_at_risk',
        'eligibility_rate',
        'avg_academic_health_score',
        'active_sessions',
        'busiest_classroom',
        'busiest_classroom_count',
        'weekly_engagement',
        'analytics_date'
    ];

    protected $casts = [
        'weekly_engagement' => 'array',
        'analytics_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
