<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'lecturer_id',
        'course_code',
        'course_name',
        'credits',
        'semester',
        'academic_year',
        'schedule_day',
        'schedule_time',
        'schedule_end_time',
        'room',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function attendanceEvaluations()
    {
        return $this->hasMany(AttendanceEvaluation::class);
    }

    public function riskPredictions()
    {
        return $this->hasMany(RiskPrediction::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function peerBenchmarks()
    {
        return $this->hasMany(PeerBenchmark::class);
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function lecturerInsights()
    {
        return $this->hasMany(LecturerInsight::class);
    }

    public function interventionLogs()
    {
        return $this->hasMany(InterventionLog::class);
    }
}
