<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full system access', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Lecturer', 'slug' => 'lecturer', 'description' => 'Course and attendance management', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'View attendance and dashboard', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
