<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'credits',
        'department_id',
        'lecturer_id',
        'lecturer_name',
        'semester',
        'academic_year',
        'room',
        'schedule',
        'is_active',
        'qr_mode',
        'semester_qr_token',
        'semester_qr_updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'semester_qr_updated_at' => 'datetime',
    ];

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

    public function getSemesterQrUrl()
    {
        if (!$this->semester_qr_token) {
            $this->regenerateSemesterQr();
        }
        $baseUrl = 'https://192.168.1.16:8443';
        return $baseUrl . '/student/scan/semester?token=' . $this->semester_qr_token . '&course=' . $this->id;
    }

    public function regenerateSemesterQr()
    {
        $this->semester_qr_token = hash('sha256', $this->id . $this->course_code . time() . rand(100000, 999999) . uniqid());
        $this->semester_qr_updated_at = now();
    }

    public function getEnrolledStudentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'approved')->count();
    }

    public function getAttendanceRateAttribute()
    {
        $totalStudents = $this->enrolled_students_count;
        if ($totalStudents == 0) return 0;

        $totalSessions = $this->attendanceSessions()->count();
        if ($totalSessions == 0) return 0;

        $totalAttendance = 0;
        foreach ($this->attendanceSessions as $session) {
            $totalAttendance += $session->present_count;
        }

        return round(($totalAttendance / ($totalSessions * $totalStudents)) * 100);
    }
}
