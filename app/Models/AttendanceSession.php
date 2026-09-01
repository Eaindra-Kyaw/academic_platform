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
        'is_cancelled' => 'boolean',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

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

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    public function scopeSemester($query)
    {
        return $query->where('qr_mode', 'semester');
    }

    public function scopeDynamic($query)
    {
        return $query->where('qr_mode', 'session');
    }

    public function scopeForLecturer($query, $lecturerId)
    {
        return $query->where('lecturer_id', $lecturerId);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('session_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('session_date', '<=', $to);
        }
        return $query;
    }

    // ============================================================
    // ATTRIBUTE ACCESSORS
    // ============================================================

    /**
     * Check if session is expired
     */
    public function isExpired()
    {
        // Semester QR never expires
        if ($this->qr_mode === 'semester') {
            return false;
        }
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Check if session is active
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Get present count
     */
    public function getPresentCountAttribute()
    {
        return $this->records()->where('status', 'present')->count();
    }

    /**
     * Get late count
     */
    public function getLateCountAttribute()
    {
        return $this->records()->where('status', 'late')->count();
    }

    /**
     * Get absent count
     */
    public function getAbsentCountAttribute()
    {
        $total = $this->total_students ?? $this->getTotalStudentsAttribute();
        return max(0, $total - $this->present_count - $this->late_count);
    }

    /**
     * Get total students enrolled in this course
     */
    public function getTotalStudentsAttribute()
    {
        return Enrollment::where('course_id', $this->course_id)
            ->where('status', 'approved')
            ->count();
    }

    /**
     * Get attendance percentage
     */
    public function getAttendancePercentageAttribute()
    {
        $total = $this->total_students;
        if ($total == 0) return 0;
        $present = $this->present_count;
        return round(($present / $total) * 100);
    }

    /**
     * Get student attendance stats (for all students in this session)
     *
     * ✅ NEW: Comprehensive stats with per-student breakdown
     */
    public function getStudentAttendanceStatsAttribute()
    {
        $total = $this->total_students;
        $present = $this->records->where('status', 'present')->count();
        $late = $this->records->where('status', 'late')->count();
        $absent = max(0, $total - $present - $late);

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'percentage' => $total > 0 ? round((($present + $late) / $total) * 100) : 0,
        ];
    }

    /**
     * ✅ NEW: Get all students with their attendance status for this session
     */
    public function getStudentsWithAttendanceAttribute()
    {
        $enrolledStudents = Enrollment::where('course_id', $this->course_id)
            ->where('status', 'approved')
            ->with('student')
            ->get();

        $students = [];
        foreach ($enrolledStudents as $enrollment) {
            $record = $this->records->firstWhere('student_id', $enrollment->student_id);

            // Get latest evaluation for this student-course
            $evaluation = AttendanceEvaluation::where('student_id', $enrollment->student_id)
                ->where('course_id', $this->course_id)
                ->orderBy('evaluation_date', 'desc')
                ->first();

            $students[] = (object) [
                'id' => $enrollment->student_id,
                'name' => $enrollment->student->name,
                'student_id' => $enrollment->student->student_id ?? 'N/A',
                'email' => $enrollment->student->email,
                'status' => $record ? $record->status : 'absent',
                'scanned_at' => $record ? $record->scanned_at : null,
                'is_manual' => $record ? $record->is_manual : false,
                'attendance_percentage' => $evaluation ? $evaluation->attendance_percentage : 0,
                'eligibility' => $evaluation ? $evaluation->eligibility_status : 'not_eligible',
                'risk_level' => $evaluation ? $evaluation->risk_level : 'Low',
            ];
        }

        // Sort: present first, then late, then absent
        usort($students, function ($a, $b) {
            $order = ['present' => 0, 'late' => 1, 'absent' => 2];
            return ($order[$a->status] ?? 3) - ($order[$b->status] ?? 3);
        });

        return $students;
    }

    /**
     * Get the QR URL for this session
     */
    public function getQRUrl()
    {
        $baseUrl = config('app.url');

        if ($this->qr_mode === 'semester') {
            return $baseUrl . '/student/scan/semester?token=' . $this->session_token . '&course=' . $this->course_id;
        }

        return $baseUrl . '/student/scan/process?token=' . $this->session_token . '&session=' . $this->id;
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Generate a unique session token
     */
    public static function generateSessionToken()
    {
        do {
            $token = hash('sha256', Str::random(32) . time() . uniqid());
        } while (self::where('session_token', $token)->exists());
        return $token;
    }

    /**
     * Generate a unique session code (6 chars)
     */
    public static function generateSessionCode()
    {
        do {
            $code = strtoupper(substr(Str::random(6), 0, 6));
        } while (self::where('manual_code', $code)->exists());
        return $code;
    }

    /**
     * ✅ NEW: Get formatted duration string
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->qr_mode === 'semester') {
            return 'Semester (No Expiry)';
        }

        $minutes = $this->duration ?? 0;
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
    }

    /**
     * ✅ NEW: Get status with color for display
     */
    public function getStatusDisplayAttribute()
    {
        return [
            'active' => [
                'label' => 'Active',
                'class' => 'active',
                'icon' => 'bi-circle-fill',
                'color' => '#10b981',
            ],
            'ended' => [
                'label' => 'Ended',
                'class' => 'ended',
                'icon' => 'bi-stop-circle-fill',
                'color' => '#6b7280',
            ],
        ][$this->status] ?? [
            'label' => ucfirst($this->status),
            'class' => 'secondary',
            'icon' => 'bi-dash-circle',
            'color' => '#6b7280',
        ];
    }

    /**
     * ✅ NEW: Get QR mode display
     */
    public function getQrModeDisplayAttribute()
    {
        return [
            'session' => [
                'label' => 'Dynamic QR',
                'icon' => 'bi-qr-code',
                'class' => 'dynamic',
            ],
            'semester' => [
                'label' => 'Semester QR',
                'icon' => 'bi-infinity',
                'class' => 'semester',
            ],
        ][$this->qr_mode] ?? [
            'label' => ucfirst($this->qr_mode),
            'icon' => 'bi-qr-code',
            'class' => 'default',
        ];
    }

    /**
     * ✅ NEW: Check if session has any records
     */
    public function hasRecords()
    {
        return $this->records()->exists();
    }

    /**
     * ✅ NEW: Get record count
     */
    public function getRecordCountAttribute()
    {
        return $this->records()->count();
    }
}
