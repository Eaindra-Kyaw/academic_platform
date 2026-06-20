<?php
// database/seeders/SemesterSeeder.php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        Semester::truncate();

        $semesters = [];
        $currentYear = 2025;
        $currentMonth = Carbon::now()->month;

        for ($year = 1; $year <= 6; $year++) {
            $baseYear = $currentYear + $year - 1;

            // First Semester: December to March (NO COURSES)
            $startDate1 = Carbon::create($baseYear, 12, 1);
            $endDate1 = Carbon::create($baseYear + 1, 3, 31);

            // Second Semester: June to September (HAS COURSES)
            $startDate2 = Carbon::create($baseYear + 1, 6, 1);
            $endDate2 = Carbon::create($baseYear + 1, 9, 30);

            // Only Second Semesters have courses, so only they should be active
            // First Semester is always inactive (0 courses)
            $isActive1 = false;
            $isActive2 = true;

            // Current semester: First Year - Second Semester (if we're in 2026)
            $isCurrent = ($year == 1 && $semester == 2);

            $semesters[] = [
                'year' => $year,
                'semester' => 1,
                'code' => 'Y' . $year . 'S1',
                'academic_year' => $currentYear . '-' . ($currentYear + 1),
                'start_date' => $startDate1,
                'end_date' => $endDate1,
                'is_active' => $isActive1,
                'is_current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $semesters[] = [
                'year' => $year,
                'semester' => 2,
                'code' => 'Y' . $year . 'S2',
                'academic_year' => $currentYear . '-' . ($currentYear + 1),
                'start_date' => $startDate2,
                'end_date' => $endDate2,
                'is_active' => $isActive2,
                'is_current' => $isCurrent,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Semester::insert($semesters);

        $this->command->info('✅ 12 semesters created successfully!');
        $this->command->info('📚 Only Second Semester of each year is active (has courses).');
        $this->command->info('⭐ First Year - Second Semester is set as current.');
    }
}
