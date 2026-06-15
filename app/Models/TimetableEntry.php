<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimetableEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'room',
        'day_of_week',
        'start_time',
        'end_time',
        'lecturer_name',
        'academic_year',
        'semester',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getDayNameAttribute()
    {
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];
        return $days[$this->day_of_week] ?? 'Unknown';
    }

    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $end->diffInMinutes($start);
    }

    public function isActiveNow()
    {
        $now = Carbon::now();
        return $this->day_of_week == $now->dayOfWeek &&
               $now->between(Carbon::parse($this->start_time), Carbon::parse($this->end_time));
    }
}
