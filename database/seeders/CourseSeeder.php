<?php
// database/seeders/CourseSeeder.php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get department IDs using 'code' as identifier
        $depts = Department::pluck('id', 'code')->toArray();

        $this->command->info('Available departments: ' . implode(', ', array_keys($depts)));

        $courses = [
            // ============================================
            // CEIT - First Year (Semester II) 2025-2026
            // Room 8-6
            // ============================================
            [
                'course_code' => 'M-2011',
                'course_name' => 'Myanmar (II)',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Thida Hnin, Daw Nilar',
                'credits' => 3,
                'year' => '1',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-6',
            ],
            [
                'course_code' => 'E-2011',
                'course_name' => 'English',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Khin Pyae Sone, Daw Khin Moe Hein',
                'credits' => 3,
                'year' => '1',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-6',
            ],
            [
                'course_code' => 'EM-2011',
                'course_name' => 'Engineering Mathematics',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Thin Thin Khaing',
                'credits' => 3,
                'year' => '1',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-6',
            ],
            [
                'course_code' => 'EPh-2001',
                'course_name' => 'Engineering Physics',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Chaw Su Myint, Daw Maw Maw San, Daw Yamin Thawdar, Dr. Htet Htet Wai Moe',
                'credits' => 4,
                'year' => '1',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-6',
            ],
            [
                'course_code' => 'ME-2002',
                'course_name' => 'Workshop',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Not Assigned',
                'credits' => 2,
                'year' => '1',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-6',
            ],
            [
                'course_code' => 'CEIT-2001',
                'course_name' => 'C Programming',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Aye Mon',
                'credits' => 3,
                'year' => '1',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-6',
            ],

            // ============================================
            // CEIT - Second Year (Semester IV) 2025-2026
            // Room S-11
            // ============================================
            [
                'course_code' => 'E-4032',
                'course_name' => 'English IV',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Ei Ei Nway',
                'credits' => 3,
                'year' => '2',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => 'S-11',
            ],
            [
                'course_code' => 'EM-4012',
                'course_name' => 'Engineering Mathematics IV',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Aye Nandar Myint, Daw Thuzar Soe',
                'credits' => 3,
                'year' => '2',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => 'S-11',
            ],
            [
                'course_code' => 'CEIT-4021',
                'course_name' => 'Advanced Java Programming',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Ei Phyu Sin Win',
                'credits' => 3,
                'year' => '2',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => 'S-11',
            ],
            [
                'course_code' => 'CEIT-4031',
                'course_name' => 'Database Management System',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Ei Mi Mi Myaing',
                'credits' => 3,
                'year' => '2',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => 'S-11',
            ],
            [
                'course_code' => 'CEIT-4041',
                'course_name' => 'Data Structure and Algorithms',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Aye Myat Thu',
                'credits' => 3,
                'year' => '2',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => 'S-11',
            ],
            [
                'course_code' => 'CEIT-4002',
                'course_name' => 'Digital Communications',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Phyo Thu Zar Tun',
                'credits' => 3,
                'year' => '2',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => 'S-11',
            ],

            // ============================================
            // CEIT - Third Year (Second Semester) 2025-2026
            // Room 1-3/1
            // ============================================
            [
                'course_code' => 'EM-32006',
                'course_name' => 'Engineering Mathematics VI',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Su Win, Daw Cho Lwin Aye',
                'credits' => 3,
                'year' => '3',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3/1',
            ],
            [
                'course_code' => 'CEIT-32012',
                'course_name' => 'Graph Theory',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Aye Myat Thu',
                'credits' => 3,
                'year' => '3',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3/1',
            ],
            [
                'course_code' => 'CEIT-32014',
                'course_name' => 'Programming Language IV',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Ei Phyu Sin Win',
                'credits' => 3,
                'year' => '3',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3/1',
            ],
            [
                'course_code' => 'CEIT-32035',
                'course_name' => 'Database Management System II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Ei Mi Mi Myaing',
                'credits' => 3,
                'year' => '3',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3/1',
            ],
            [
                'course_code' => 'CEIT-32060',
                'course_name' => 'Data Communication and Networking II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Khin Ohnmar Maung',
                'credits' => 3,
                'year' => '3',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3/1',
            ],
            [
                'course_code' => 'CEIT-32061',
                'course_name' => 'Digital Communications',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Phyo Thu Zar Tun',
                'credits' => 3,
                'year' => '3',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3/1',
            ],

            // ============================================
            // CEIT - Fourth Year (Second Semester) 2025-2026
            // Room 1-3-7
            // ============================================
            [
                'course_code' => 'EM-42008',
                'course_name' => 'Engineering Mathematics VIII',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Khin Ma Ma Moe',
                'credits' => 3,
                'year' => '4',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3-7',
            ],
            [
                'course_code' => 'HSS-42011',
                'course_name' => 'Humanities and Social Science',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Ei Phyu Sin Win',
                'credits' => 3,
                'year' => '4',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3-7',
            ],
            [
                'course_code' => 'CEIT-42016',
                'course_name' => 'Digital Signal Processing',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Khin Ohnmar Maung',
                'credits' => 3,
                'year' => '4',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3-7',
            ],
            [
                'course_code' => 'CEIT-42022',
                'course_name' => 'Information Theory II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Khin Ohnmar Maung',
                'credits' => 3,
                'year' => '4',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3-7',
            ],
            [
                'course_code' => 'CEIT-42024',
                'course_name' => 'Software Engineering II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Nay Min Htaik',
                'credits' => 3,
                'year' => '4',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3-7',
            ],
            [
                'course_code' => 'CEIT-42014',
                'course_name' => 'Programming Language IV',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Ei Phyu Sin Win',
                'credits' => 3,
                'year' => '4',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3-7',
            ],
            [
                'course_code' => 'CEIT-42017',
                'course_name' => 'Cyber Forensics',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Aye Myat Thu',
                'credits' => 3,
                'year' => '4',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '1-3-7',
            ],

            // ============================================
            // CEIT - Fifth Year (Second Semester) 2025-2026
            // Room 8-5
            // ============================================
            [
                'course_code' => 'E-52001',
                'course_name' => 'English (T2)',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Thida Aye',
                'credits' => 3,
                'year' => '5',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-5',
            ],
            [
                'course_code' => 'CEIT-52061',
                'course_name' => 'Embedded System II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Nay Min Htaik',
                'credits' => 3,
                'year' => '5',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-5',
            ],
            [
                'course_code' => 'CEIT-52010',
                'course_name' => 'Wireless and Mobile Communication II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Aye Mon',
                'credits' => 3,
                'year' => '5',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-5',
            ],
            [
                'course_code' => 'CEIT-52023',
                'course_name' => 'Computer Architecture II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Theingi Myint',
                'credits' => 3,
                'year' => '5',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-5',
            ],
            [
                'course_code' => 'CEIT-52033',
                'course_name' => 'Machine Learning',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Phyo Thu Zar Tun',
                'credits' => 3,
                'year' => '5',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-5',
            ],
            [
                'course_code' => 'CEIT-52026',
                'course_name' => 'Digital Image Processing II',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Daw Khin Poe Ou',
                'credits' => 3,
                'year' => '5',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-5',
            ],
            [
                'course_code' => 'CEIT-52018',
                'course_name' => 'Project Management',
                'department_code' => 'CEIT', // Changed from 'CS' to 'CEIT'
                'lecturer_name' => 'Dr. Theingi Myint',
                'credits' => 3,
                'year' => '5',
                'semester' => 'Second Semester',
                'academic_year' => '2025-2026',
                'room' => '8-5',
            ],
        ];

        $count = 0;
        $skipped = 0;

        foreach ($courses as $course) {
            $departmentId = $depts[$course['department_code']] ?? null;

            if ($departmentId) {
                Course::updateOrCreate(
                    ['course_code' => $course['course_code']],
                    [
                        'course_name' => $course['course_name'],
                        'department_id' => $departmentId,
                        'lecturer_name' => $course['lecturer_name'],
                        'credits' => $course['credits'],
                        'year' => $course['year'],
                        'semester' => $course['semester'],
                        'academic_year' => $course['academic_year'],
                        'room' => $course['room'] ?? null,
                        'is_active' => true,
                    ]
                );
                $count++;
                $this->command->info("✅ Added course: " . $course['course_code'] . " - " . $course['course_name']);
            } else {
                $this->command->warn("⚠️ Skipped: {$course['course_code']} - Department '{$course['department_code']}' not found");
                $skipped++;
            }
        }

        $this->command->info("🎉 Course seeding completed!");
        $this->command->info("✅ $count courses seeded successfully!");
        if ($skipped > 0) {
            $this->command->warn("⚠️ $skipped courses skipped.");
        }
    }
}
