<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'role_id',
    'department_id',
    'student_id',
    'name',
    'email',
    'password',
    'profile_picture',
    'phone',
    'address',
    'current_year',
    'enrollment_year',
    'is_active',
    'must_change_password',
    'password_changed_at',
    'remember_token',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Lecturer Relationships

    public function coursesTeaching()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class, 'lecturer_id');
    }

    public function lecturerInsights()
    {
        return $this->hasMany(LecturerInsight::class, 'lecturer_id');
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class, 'lecturer_id');
    }

    // Student Relationships

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    public function attendanceEvaluations()
    {
        return $this->hasMany(AttendanceEvaluation::class, 'student_id');
    }

    public function riskPredictions()
    {
        return $this->hasMany(RiskPrediction::class, 'student_id');
    }

    public function academicHealthScores()
    {
        return $this->hasMany(AcademicHealthScore::class, 'student_id');
    }

    public function peerBenchmarks()
    {
        return $this->hasMany(PeerBenchmark::class, 'student_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function chatbotLogs()
    {
        return $this->hasMany(ChatbotLog::class);
    }

    public function interventionLogs()
    {
        return $this->hasMany(InterventionLog::class, 'student_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'posted_by');
    }
}
