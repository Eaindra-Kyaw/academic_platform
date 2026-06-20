<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'details',        // ✅ Uses 'details' column from your database
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Main logging method
    public static function log($userId, $action, $details = null, $model = null, $status = 'success')
    {
        $detailsData = is_array($details) ? json_encode($details) : $details;

        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'details' => $detailsData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $status,
        ]);
    }

    // Accessors
    public function getDetailsArrayAttribute()
    {
        return json_decode($this->details, true) ?? [];
    }

    public function getActionLabelAttribute()
    {
        $labels = [
            'login' => 'Login',
            'logout' => 'Logout',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'create_session' => 'Create Session',
            'end_session' => 'End Session',
            'refresh_qr' => 'Refresh QR',
            'regenerate_semester_qr' => 'Regenerate Semester QR',
            'manual_attendance' => 'Manual Attendance',
            'manual_attendance_failed' => 'Manual Attendance Failed',
        ];

        return $labels[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'success' => 'success',
            'failed' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}
