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
        'session_code',
        'manual_code',
        'room',
        'duration',
        'status',
        'started_at',
        'start_time',
        'session_date',
        'expires_at',
        'qr_expires_at',
        'total_students',
        'present_count',
        'late_count',
        'absent_count',
    ];

    protected $casts = [
        'qr_expires_at' => 'datetime',
        'session_date' => 'date',
        'start_time' => 'datetime',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
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
                     ->where('qr_expires_at', '>', now());
    }

    public function isExpired()
    {
        return $this->qr_expires_at && now()->gt($this->qr_expires_at);
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function getAttendancePercentageAttribute()
    {
        $total = $this->total_students ?? 0;
        if ($total == 0) return 0;
        $present = $this->present_count ?? 0;
        return round(($present / $total) * 100);
    }

    public static function generateSessionToken()
    {
        do {
            $token = hash('sha256', Str::random(32) . time() . uniqid());
        } while (self::where('session_token', $token)->exists());
        return $token;
    }

    public static function generateSessionCode()
    {
        do {
            $code = strtoupper(substr(Str::random(6), 0, 6));
        } while (self::where('session_code', $code)->exists());
        return $code;
    }

    public function getQRUrl()
    {
        $baseUrl = 'https://192.168.1.16:8443';
        return $baseUrl . '/student/scan/process?token=' . $this->session_token . '&session=' . $this->id;
    }
}
