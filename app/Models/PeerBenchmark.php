<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeerBenchmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'student_attendance',
        'course_avg_attendance',
        'department_avg_attendance',
        'university_avg_attendance',
        'attendance_rank',
        'total_students_in_course',
        'student_health_score',
        'course_avg_health_score',
        'benchmark_date'
    ];

    protected $casts = [
        'benchmark_date' => 'date',
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
