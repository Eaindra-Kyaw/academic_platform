<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $csDept = Department::where('code', 'CS')->first();
        $lecturer = User::where('role_id', 2)->first();

        $courses = [
            [
                'department_id' => $csDept->id,
                'lecturer_id' => $lecturer->id,
                'course_code' => 'CS301',
                'course_name' => 'Database Systems',
                'credits' => 3,
                'semester' => 6,
                'academic_year' => 2024,
                'schedule_day' => 'Monday',
                'schedule_time' => '09:00:00',
                'schedule_end_time' => '10:30:00',
                'room' => 'A-203',
                'is_active' => true,
            ],
            [
                'department_id' => $csDept->id,
                'lecturer_id' => $lecturer->id,
                'course_code' => 'CS302',
                'course_name' => 'Computer Networks',
                'credits' => 3,
                'semester' => 6,
                'academic_year' => 2024,
                'schedule_day' => 'Tuesday',
                'schedule_time' => '11:00:00',
                'schedule_end_time' => '12:30:00',
                'room' => 'B-101',
                'is_active' => true,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
