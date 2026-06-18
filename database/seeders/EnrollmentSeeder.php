<?php
// database/seeders/EnrollmentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get fifth year students from CEIT department
        $students = DB::table('users')
            ->where('role_id', 3)
            ->where('current_year', 5)
            ->where('is_active', true)
            ->get();

        // Get fifth year courses
        $courses = DB::table('courses')
            ->where('year', 'Fifth Year')
            ->get();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('⚠️ No students or courses found for fifth year.');
            return;
        }

        $this->command->info("Found " . $students->count() . " fifth year students and " . $courses->count() . " courses.");

        foreach ($students as $student) {
            foreach ($courses as $course) {
                // Random attendance between 65-98
                $attendance = rand(65, 98);

                // Roll call calculation based on attendance
                $rollCall = match(true) {
                    $attendance >= 95 => 10,
                    $attendance >= 90 => 9,
                    $attendance >= 85 => 8,
                    $attendance >= 80 => 7,
                    $attendance >= 75 => 6,
                    $attendance >= 70 => 5,
                    $attendance >= 65 => 4,
                    $attendance >= 60 => 3,
                    $attendance >= 55 => 2,
                    default => 1,
                };

                // Eligibility based on attendance
                $eligibility = $attendance >= 75 ? 'eligible' : ($attendance >= 60 ? 'warning' : 'not_eligible');

                // Check if enrollment exists
                $exists = DB::table('enrollments')
                    ->where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->exists();

                if (!$exists) {
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
                } else {
                    DB::table('enrollments')
                        ->where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->update([
                            'attendance_percentage' => $attendance,
                            'roll_call_mark' => $rollCall,
                            'eligibility_status' => $eligibility,
                            'updated_at' => Carbon::now(),
                        ]);
                }
            }
        }

        $this->command->info('✅ Fifth year enrollments seeded successfully!');
    }
}
