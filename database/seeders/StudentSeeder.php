<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            // Computer Engineering
            ['name' => 'Min Thu Kyaw', 'email' => 'minthu.kyaw@mtu.edu.mm', 'dept_code' => 'CS', 'roll' => 'CS-001', 'semester' => 6],
            ['name' => 'Ei Ei Khaing', 'email' => 'eiei.khaing@mtu.edu.mm', 'dept_code' => 'CS', 'roll' => 'CS-002', 'semester' => 6],
            ['name' => 'Kaung Myat Thu', 'email' => 'kaungmyatthu@mtu.edu.mm', 'dept_code' => 'CS', 'roll' => 'CS-003', 'semester' => 6],
            ['name' => 'Su Mon Aung', 'email' => 'sumonaung@mtu.edu.mm', 'dept_code' => 'CS', 'roll' => 'CS-004', 'semester' => 6],
            ['name' => 'Hein Htet Zan', 'email' => 'heinhtetzan@mtu.edu.mm', 'dept_code' => 'CS', 'roll' => 'CS-005', 'semester' => 6],

            // Electronic Engineering
            ['name' => 'Aung Thura', 'email' => 'aungthura@mtu.edu.mm', 'dept_code' => 'EC', 'roll' => 'EC-001', 'semester' => 6],
            ['name' => 'Thin Thin Aung', 'email' => 'thinthinaung@mtu.edu.mm', 'dept_code' => 'EC', 'roll' => 'EC-002', 'semester' => 6],

            // Information Technology
            ['name' => 'Thura Min', 'email' => 'thuramin@mtu.edu.mm', 'dept_code' => 'IT', 'roll' => 'IT-001', 'semester' => 6],
            ['name' => 'La Pyae Moe', 'email' => 'lapyaemoe@mtu.edu.mm', 'dept_code' => 'IT', 'roll' => 'IT-002', 'semester' => 6],
            ['name' => 'Zwe Htet Naing', 'email' => 'zwehtetnaing@mtu.edu.mm', 'dept_code' => 'IT', 'roll' => 'IT-003', 'semester' => 6],

            // Mechanical Engineering
            ['name' => 'Thet Naing', 'email' => 'thetnaing@mtu.edu.mm', 'dept_code' => 'ME', 'roll' => 'ME-001', 'semester' => 6],
            ['name' => 'Su Hlaing', 'email' => 'suhlaing@mtu.edu.mm', 'dept_code' => 'ME', 'roll' => 'ME-002', 'semester' => 6],
        ];

        foreach ($students as $student) {
            $dept = DB::table('departments')->where('code', $student['dept_code'])->first();

            DB::table('users')->insert([
                'role_id' => 3,
                'department_id' => $dept ? $dept->id : null,
                'student_id' => $student['roll'],
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password123'),
                'semester' => $student['semester'],
                'enrollment_year' => 2022,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
