<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_session_id',
        'scanned_at',
        'status',
        'is_manual',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'is_manual' => 'boolean',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }

    public function scopeAuto($query)
    {
        return $query->where('is_manual', false);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'present' => 'green',
            'late' => 'yellow',
            'absent' => 'red',
            default => 'gray',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'absent' => 'danger',
            default => 'secondary',
        };
    }
}
