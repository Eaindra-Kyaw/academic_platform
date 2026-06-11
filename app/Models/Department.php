<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'head_of_department',
        'description'
    ];

    /**
     * Get the users (students and lecturers) belonging to this department
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the students in this department
     */
    public function students()
    {
        return $this->hasMany(User::class)->where('role_id', 3);
    }

    /**
     * Get the lecturers in this department
     */
    public function lecturers()
    {
        return $this->hasMany(User::class)->where('role_id', 2);
    }

    /**
     * Get the courses offered by this department
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get university analytics for this department
     */
    public function analytics()
    {
        return $this->hasMany(UniversityAnalytics::class);
    }
}
