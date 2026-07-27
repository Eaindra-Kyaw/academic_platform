<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_name',
        'semester_number',
        'semester_name',
        'code',
        'academic_year',
        'start_date',
        'end_date',
        'is_active',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->year_name . ' - ' . $this->semester_name;
    }

    public function getDisplayNameAttribute()
    {
        return $this->academic_year . ' - ' . $this->semester_name;
    }

    public function getDateRangeAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('d M Y') . ' - ' . $this->end_date->format('d M Y');
        }
        return 'N/A';
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_current) {
            return '<span class="badge badge-success">⭐ Current</span>';
        } elseif ($this->is_active) {
            return '<span class="badge badge-primary">✅ Active</span>';
        } else {
            return '<span class="badge badge-secondary">❌ Inactive</span>';
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
