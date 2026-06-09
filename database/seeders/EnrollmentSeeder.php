<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('users')->where('role_id', 3)->get();
        $courses = DB::table('courses')->get();

        foreach ($students as $student) {
            // Get courses from same department
            $deptCourses = $courses->where('department_id', $student->department_id);

            foreach ($deptCourses as $course) {
                $attendance = rand(65, 98);
                $rollCall = $attendance >= 95 ? 10 : ($attendance >= 90 ? 9 : ($attendance >= 85 ? 8 : ($attendance >= 80 ? 7 : ($attendance >= 75 ? 6 : ($attendance >= 70 ? 5 : ($attendance >= 65 ? 4 : ($attendance >= 60 ? 3 : ($attendance >= 55 ? 2 : 1))))))));
                $eligibility = $attendance >= 75 ? 'eligible' : ($attendance >= 60 ? 'warning' : 'not_eligible');

                DB::table('enrollments')->insert([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'enrollment_date' => Carbon::now(),
                    'status' => 'approved',
                    'attendance_percentage' => $attendance,
                    'roll_call_mark' => $rollCall,
                    'eligibility_status' => $eligibility,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
