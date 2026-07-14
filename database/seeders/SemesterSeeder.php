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
            $isActive1 = false;
            $isActive2 = true;

            // ✅ FIXED: Use $year directly (not $semester variable)
            $isCurrent = ($year == 1); // First Year is current

            $semesters[] = [
                'year_name' => $this->getYearName($year),
                'semester_number' => 1,
                'semester_name' => 'First Semester',
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
                'year_name' => $this->getYearName($year),
                'semester_number' => 2,
                'semester_name' => 'Second Semester',
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

    private function getYearName($yearNumber)
    {
        $map = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];
        return $map[$yearNumber] ?? 'Unknown Year';
    }
}
