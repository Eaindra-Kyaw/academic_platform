<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'course_id', 'enrollment_date', 'status', 'approved_at', 'dropped_at',
        'attendance_percentage', 'roll_call_mark', 'eligibility_status'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'approved_at' => 'datetime',
        'dropped_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
