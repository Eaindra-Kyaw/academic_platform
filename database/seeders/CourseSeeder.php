<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            // Computer Engineering
            ['code' => 'CS301', 'name' => 'Database Management Systems', 'dept_code' => 'CS', 'credits' => 3],
            ['code' => 'CS302', 'name' => 'Computer Networks', 'dept_code' => 'CS', 'credits' => 3],
            ['code' => 'CS303', 'name' => 'Operating Systems', 'dept_code' => 'CS', 'credits' => 3],
            ['code' => 'CS304', 'name' => 'Web Development', 'dept_code' => 'CS', 'credits' => 3],

            // Electronic Engineering
            ['code' => 'EC301', 'name' => 'Digital Signal Processing', 'dept_code' => 'EC', 'credits' => 3],
            ['code' => 'EC302', 'name' => 'Microcontroller Systems', 'dept_code' => 'EC', 'credits' => 3],

            // Information Technology
            ['code' => 'IT301', 'name' => 'Data Structures', 'dept_code' => 'IT', 'credits' => 3],
            ['code' => 'IT302', 'name' => 'Mobile App Development', 'dept_code' => 'IT', 'credits' => 3],
            ['code' => 'IT303', 'name' => 'Cloud Computing', 'dept_code' => 'IT', 'credits' => 3],

            // Mechanical Engineering
            ['code' => 'ME301', 'name' => 'Thermodynamics', 'dept_code' => 'ME', 'credits' => 3],
            ['code' => 'ME302', 'name' => 'Fluid Mechanics', 'dept_code' => 'ME', 'credits' => 3],
        ];

        foreach ($courses as $course) {
            $dept = DB::table('departments')->where('code', $course['dept_code'])->first();

            // Get a random lecturer from same department
            $lecturer = DB::table('users')
                ->where('role_id', 2)
                ->where('department_id', $dept->id)
                ->inRandomOrder()
                ->first();

            DB::table('courses')->insert([
                'department_id' => $dept->id,
                'lecturer_id' => $lecturer ? $lecturer->id : null,
                'course_code' => $course['code'],
                'course_name' => $course['name'],
                'credits' => $course['credits'],
                'semester' => 6,
                'academic_year' => 2024,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
