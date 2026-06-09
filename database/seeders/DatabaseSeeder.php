<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        DB::table('roles')->insert([
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full system access', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Lecturer', 'slug' => 'lecturer', 'description' => 'Course and attendance management', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'View attendance and dashboard', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // Create Admin User
        DB::table('users')->insert([
            'role_id' => 1,
            'name' => 'Admin User',
            'email' => 'admin1@mtu.edu',
            'password' => Hash::make('admin001'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create Lecturer - Dr. Phyo Thu Zar Tun
        DB::table('users')->insert([
            'role_id' => 2,
            'name' => 'Dr. Phyo Thu Zar Tun',
            'email' => 'phyothuzartun@mtu.edu.mm',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create Student - Eaindra Kyaw
        DB::table('users')->insert([
            'role_id' => 3,
            'name' => 'Eaindra Kyaw',
            'email' => 'eaindrakyaw@mtu.edu.mm',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info('Database seeded with:');
        $this->command->info('  - Admin: admin1@mtu.edu / admin001');
        $this->command->info('  - Lecturer: phyothuzartun@mtu.edu.mm / password123 (Dr. Phyo Thu Zar Tun)');
        $this->command->info('  - Student: eaindrakyaw@mtu.edu.mm / password123 (Eaindra Kyaw)');
    }
}
