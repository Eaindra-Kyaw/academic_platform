<?php
// app/Models/Announcement.php

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

    public function scopeForRole($query, $role)
    {
        return $query->where('target_role', 'all')
            ->orWhere('target_role', $role);
    }

    // Helper methods
    public function getAudienceLabelAttribute()
    {
        return match($this->target_role) {
            'all' => 'All Users',
            'admin' => 'Admins',
            'lecturer' => 'Lecturers',
            'student' => 'Students',
            default => ucfirst($this->target_role),
        };
    }

    public function getTargetRoleLabelAttribute()
    {
        return $this->getAudienceLabelAttribute();
    }
}
