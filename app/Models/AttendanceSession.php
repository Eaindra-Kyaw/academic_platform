<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AttendanceSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'course_id',
    'lecturer_id',
    'session_token',
    'manual_code',
    'session_code',
    'session_date',
    'period_count',
    'conducted_periods',
    'is_cancelled',
    'cancellation_reason',
    'qr_mode',
    'room',
    'duration',
    'status',
    'started_at',
    'expires_at',
    'qr_expires_at',
    'ended_at',
    'present_count',
    'late_count',
    'total_students',
];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'ended_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class, 'attendance_session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '>', now());
    }

    public function isExpired()
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    // Generate SHA-256 token for QR
    public static function generateSessionToken()
    {
        do {
            $token = hash('sha256', Str::random(32) . time() . uniqid());
        } while (self::where('session_token', $token)->exists());
        return $token;
    }

    // Generate 6-character manual code
    public static function generateSessionCode()
    {
        do {
            $code = strtoupper(substr(Str::random(6), 0, 6));
        } while (self::where('manual_code', $code)->exists());
        return $code;
    }

    // Get QR URL for scanning
    public function getQRUrl()
    {
        $baseUrl = config('app.url');
        return $baseUrl . '/student/scan/process?token=' . $this->session_token . '&session=' . $this->id;
    }

    // Get attendance statistics from records
    public function getPresentCountAttribute()
    {
        return $this->records()->where('status', 'present')->count();
    }

    public function getLateCountAttribute()
    {
        return $this->records()->where('status', 'late')->count();
    }

    public function getAbsentCountAttribute()
    {
        return $this->records()->where('status', 'absent')->count();
    }

    public function getTotalStudentsAttribute()
    {
        return Enrollment::where('course_id', $this->course_id)
            ->where('status', 'approved')
            ->count();
    }

    public function getAttendancePercentageAttribute()
    {
        $total = $this->total_students;
        if ($total == 0) return 0;
        $present = $this->present_count;
        return round(($present / $total) * 100);
    }
}
