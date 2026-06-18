<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Constants
    const ADMIN = 1;
    const LECTURER = 2;
    const STUDENT = 3;

    // Helper methods
    public function isAdmin()
    {
        return $this->id === self::ADMIN;
    }

    public function isLecturer()
    {
        return $this->id === self::LECTURER;
    }

    public function isStudent()
    {
        return $this->id === self::STUDENT;
    }
}
