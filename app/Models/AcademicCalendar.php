<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    protected $table = 'academic_calendar';

    protected $fillable = [
        'date',
        'event_name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeHolidays($query)
    {
        return $query->where('type', 'holiday')->orWhere('type', 'public_holiday');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isHoliday()
    {
        return in_array($this->type, ['holiday', 'public_holiday', 'university_closure']);
    }
}
