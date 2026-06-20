<?php
// app/Models/Semester.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'semester',
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

    public static $yearNames = [
        1 => 'First Year',
        2 => 'Second Year',
        3 => 'Third Year',
        4 => 'Fourth Year',
        5 => 'Fifth Year',
        6 => 'Sixth Year',
    ];

    public static $semesterNames = [
        1 => 'First Semester',
        2 => 'Second Semester',
    ];

    public static $semesterMonths = [
        1 => 'December - March',
        2 => 'June - September',
    ];

    // Relationships
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Accessors
    public function getYearNameAttribute()
    {
        return self::$yearNames[$this->year] ?? 'Unknown Year';
    }

    public function getSemesterNameAttribute()
    {
        return self::$semesterNames[$this->semester] ?? 'Unknown Semester';
    }

    public function getFullNameAttribute()
    {
        return $this->year_name . ' - ' . $this->semester_name;
    }

    public function getDisplayNameAttribute()
    {
        return $this->year_name . ' (' . $this->semester_name . ')';
    }

    public function getDateRangeAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('d M Y') . ' - ' . $this->end_date->format('d M Y');
        }
        return self::$semesterMonths[$this->semester] ?? 'N/A';
    }

    public function getSemesterMonthsAttribute()
    {
        return self::$semesterMonths[$this->semester] ?? 'N/A';
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

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public static function getYearsWithCounts()
    {
        $years = [];
        for ($i = 1; $i <= 6; $i++) {
            $years[$i] = self::where('year', $i)->count();
        }
        return $years;
    }
}
