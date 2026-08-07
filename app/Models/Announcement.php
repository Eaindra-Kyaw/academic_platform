<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'target_role',
        'posted_by',
        'is_active',
        'published_at',
        'read_by',  // ✅ Added
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }

    public function scopeForRole($query, $role)
    {
        return $query->where(function($q) use ($role) {
            $q->where('target_role', 'all')
              ->orWhere('target_role', 'LIKE', "%{$role}%");
        });
    }

    /**
     * Get unread announcements count for a user
     */
    public static function getUnreadCount($user)
    {
        $announcements = self::forRole($user->role->name ?? 'student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->get();

        $unreadCount = 0;
        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($user->id)) {
                $unreadCount++;
            }
        }

        return $unreadCount;
    }

    /**
     * Mark announcement as read for a user
     */
    public function markAsRead($userId)
    {
        $readBy = $this->read_by ? explode(',', $this->read_by) : [];

        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->read_by = implode(',', $readBy);
            $this->save();
        }
    }

    /**
     * Check if announcement is read by user
     */
    public function isReadBy($userId)
    {
        if (empty($this->read_by)) {
            return false;
        }

        $readBy = explode(',', $this->read_by);
        return in_array($userId, $readBy);
    }

    public function isExpired()
    {
        return false;
    }

    public function getTargetRolesArrayAttribute()
    {
        return explode(',', $this->target_role);
    }

    public function getAudienceLabelAttribute()
    {
        $roles = $this->getTargetRolesArrayAttribute();

        if (in_array('all', $roles)) {
            return 'All Users';
        }

        $roleLabels = [];
        foreach ($roles as $role) {
            $roleLabels[] = match($role) {
                'admin' => 'Admins',
                'lecturer' => 'Lecturers',
                'student' => 'Students',
                default => ucfirst($role),
            };
        }

        return implode(' & ', $roleLabels);
    }

    public function getAudienceBadgeClassAttribute()
    {
        $roles = $this->getTargetRolesArrayAttribute();

        if (in_array('all', $roles)) {
            return 'badge-all';
        }

        if (count($roles) >= 2) {
            return 'badge-multiple';
        }

        return match($this->target_role) {
            'admin' => 'badge-admin',
            'lecturer' => 'badge-lecturer',
            'student' => 'badge-student',
            default => 'badge-all',
        };
    }

    /**
     * Reset read status for a user
     */
    public static function resetReadStatus($userId)
    {
        $announcements = self::all();

        foreach ($announcements as $announcement) {
            if ($announcement->isReadBy($userId)) {
                $readBy = explode(',', $announcement->read_by);
                $readBy = array_filter($readBy, function($id) use ($userId) {
                    return $id != $userId;
                });
                $announcement->read_by = implode(',', $readBy);
                $announcement->save();
            }
        }
    }

    /**
     * Force reset all read statuses for testing
     */
    public static function forceResetAll()
    {
        self::query()->update(['read_by' => null]);
    }
}
