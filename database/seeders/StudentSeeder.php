<?php
// database/seeders/StudentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Get Computer Engineering department ID (CS)
        $dept = DB::table('departments')->where('code', 'CEIT')->first();

        if (!$dept) {
            $this->command->error('❌ CS Department not found! Run DepartmentSeeder first.');
            return;
        }

        $students = [
            ['name' => 'Shwe Sin Phoo', 'student_id' => 'V.BE.CEIT-1', 'email' => 'shwesinphoo@mtu.edu.mm'],
            ['name' => 'Eaindra Kyaw', 'student_id' => 'V.BE.CEIT-2', 'email' => 'eaindrakyaw@mtu.edu.mm'],
            ['name' => 'Phyo Thiri Zaw', 'student_id' => 'V.BE.CEIT-3', 'email' => 'phyothirizaw@mtu.edu.mm'],
            ['name' => 'May Thu Khine', 'student_id' => 'V.BE.CEIT-4', 'email' => 'maythukhine@mtu.edu.mm'],
            ['name' => 'Hsu Wai Wai Lwin', 'student_id' => 'V.BE.CEIT-5', 'email' => 'hsuwaiwailwin@mtu.edu.mm'],
            ['name' => 'Win Lae Shwe Yee Win', 'student_id' => 'V.BE.CEIT-6', 'email' => 'winlaeshweyeewin@mtu.edu.mm'],
            ['name' => 'Nanda Aung', 'student_id' => 'V.BE.CEIT-7', 'email' => 'nandaaung@mtu.edu.mm'],
            ['name' => 'Aung Aung Min Khant', 'student_id' => 'V.BE.CEIT-8', 'email' => 'aungaungminkhant@mtu.edu.mm'],
            ['name' => 'Nyi Nyi Moe Myint', 'student_id' => 'V.BE.CEIT-9', 'email' => 'nyinyimoemyint@mtu.edu.mm'],
            ['name' => 'Bo Bo Saw', 'student_id' => 'V.BE.CEIT-10', 'email' => 'bobosaw@mtu.edu.mm'],
        ];

        foreach ($students as $student) {
            DB::table('users')->updateOrInsert(
                ['email' => $student['email']],
                [
                    'role_id' => 3,
                    'department_id' => $dept->id,
                    'name' => $student['name'],
                    'student_id' => $student['student_id'],
                    'password' => Hash::make('student123'),
                    'current_year' => 5,
                    'enrollment_year' => 2020,
                    'email_verified_at' => now(),
                    'must_change_password' => false,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

        $this->command->info('✅ 10 Fifth Year CEIT students seeded successfully!');
        $this->command->warn('📝 Default password for all students: student123');
    }
}
