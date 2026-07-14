<?php
// database/seeders/EnrollmentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Carbon;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get fifth year students
        $students = User::where('role_id', 3)
            ->where('current_year', 5)
            ->where('is_active', true)
            ->get();

        // Get fifth year courses
        $courses = Course::where('year', 'Fifth Year')->get();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('⚠️ No students or courses found for fifth year.');
            $this->command->warn('   - Students found: ' . $students->count());
            $this->command->warn('   - Courses found: ' . $courses->count());
            return;
        }

        $this->command->info("Found " . $students->count() . " students and " . $courses->count() . " courses.");

        $enrolledCount = 0;

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

                // ✅ FIXED: Only set columns that exist in the table
                // The table has: approved_at, dropped_at (but NOT rejected_at)
                $enrollment = Enrollment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'enrollment_date' => Carbon::now(),
                        'status' => 'approved',
                        'attendance_percentage' => $attendance,
                        'roll_call_mark' => $rollCall,
                        'eligibility_status' => $eligibility,
                        'approved_at' => Carbon::now(),  // ✅ Set approved date
                        'dropped_at' => null,            // ✅ No dropped date
                    ]
                );

                if ($enrollment->wasRecentlyCreated) {
                    $enrolledCount++;
                }
            }
        }

        $this->command->info('✅ ' . $enrolledCount . ' fifth year enrollments seeded successfully!');
    }
}
