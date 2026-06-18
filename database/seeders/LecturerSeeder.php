<?php
// database/seeders/LecturerSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        // Get CEIT department ID
        $ceitDept = DB::table('departments')->where('code', 'CS')->first();

        if (!$ceitDept) {
            $this->command->error('❌ CS Department not found! Run DepartmentSeeder first.');
            return;
        }

        // All CEIT Lecturers from your data
        $lecturers = [
            // CEIT Full-time Lecturers
            ['name' => 'Dr. Aye Myat Thu', 'email' => 'ayemyatthu@mtu.edu.mm', 'password' => 'ayemyat123'],
            ['name' => 'Dr. Nay Min Htaik', 'email' => 'nayminhtaik@mtu.edu.mm', 'password' => 'naymin123'],
            ['name' => 'Dr. Khin Ohnmar Maung', 'email' => 'khinohnmarmaung@mtu.edu.mm', 'password' => 'khinohnmar123'],
            ['name' => 'Dr. Theingi Myint', 'email' => 'theingimyint@mtu.edu.mm', 'password' => 'theingi123'],
            ['name' => 'Daw Ei Phyu Sin Win', 'email' => 'eiphyusinwin@mtu.edu.mm', 'password' => 'eiphyu123'],
            ['name' => 'Daw Ei Mi Mi Myaing', 'email' => 'eimimimyaing@mtu.edu.mm', 'password' => 'eimimi123'],
            ['name' => 'Daw Aye Mon', 'email' => 'ayemon@mtu.edu.mm', 'password' => 'ayemon123'],
            ['name' => 'Daw Khin Poe Ou', 'email' => 'khinpoeou@mtu.edu.mm', 'password' => 'khinpoe123'],
            ['name' => 'Dr. Khin Ma Ma Moe', 'email' => 'khinmamamoee@mtu.edu.mm', 'password' => 'khinmamoe123'],
            ['name' => 'Dr. Htet Htet Wai Moe', 'email' => 'htethtetwaimoe@mtu.edu.mm', 'password' => 'htethtet123'],
            ['name' => 'Daw Thida Aye', 'email' => 'thidaaye@mtu.edu.mm', 'password' => 'thida123'],
            ['name' => 'Daw Thida Hnin', 'email' => 'thidahnin@mtu.edu.mm', 'password' => 'thidahnin123'],
            ['name' => 'Daw Nilar', 'email' => 'nilar@mtu.edu.mm', 'password' => 'nilar123'],
            ['name' => 'Daw Khin Pyae Sone', 'email' => 'khinpyaesone@mtu.edu.mm', 'password' => 'khinpyae123'],
            ['name' => 'Daw Khin Moe Hein', 'email' => 'khinmoehein@mtu.edu.mm', 'password' => 'khinmoe123'],
            ['name' => 'Daw Thin Thin Khaing', 'email' => 'thinthinkhaing@mtu.edu.mm', 'password' => 'thinthin123'],
            ['name' => 'Daw Chaw Su Myint', 'email' => 'chawsunyint@mtu.edu.mm', 'password' => 'chawsu123'],
            ['name' => 'Daw Maw Maw San', 'email' => 'mawmawsan@mtu.edu.mm', 'password' => 'mawmaw123'],
            ['name' => 'Daw Yamin Thawdar', 'email' => 'yaminthawdar@mtu.edu.mm', 'password' => 'yamin123'],
            ['name' => 'Daw Ei Ei Nway', 'email' => 'eieinway@mtu.edu.mm', 'password' => 'eieinway123'],
            ['name' => 'Dr. Aye Nandar Myint', 'email' => 'ayenandarmyint@mtu.edu.mm', 'password' => 'ayenandar123'],
            ['name' => 'Daw Thuzar Soe', 'email' => 'thuzarsoe@mtu.edu.mm', 'password' => 'thuzar123'],
            ['name' => 'Daw Su Win', 'email' => 'suwin@mtu.edu.mm', 'password' => 'suwin123'],
            ['name' => 'Daw Cho Lwin Aye', 'email' => 'cholwinaye@mtu.edu.mm', 'password' => 'cholwin123'],
            // Other department lecturers
            ['name' => 'Dr. Su Myat Mon', 'email' => 'sumyatmon@mtu.edu.mm', 'password' => 'sumyat123', 'department_code' => 'CE'],
            ['name' => 'Dr. Nann Su Le Mya Thwin', 'email' => 'nannsulemyathwin@mtu.edu.mm', 'password' => 'nannsu123', 'department_code' => 'CE'],
            ['name' => 'Daw Mi Ko Theint', 'email' => 'mikotheint@mtu.edu.mm', 'password' => 'miko123', 'department_code' => 'CE'],
            ['name' => 'Dr. Aung Kyaw Soe', 'email' => 'aungkyawsoe@mtu.edu.mm', 'password' => 'aungkyaw123', 'department_code' => 'ME'],
        ];

        $count = 0;
        $skipped = 0;

        foreach ($lecturers as $lecturer) {
            // Determine department
            $departmentCode = $lecturer['department_code'] ?? 'CS';
            $dept = DB::table('departments')->where('code', $departmentCode)->first();

            if (!$dept) {
                $this->command->warn("⚠️ Department '{$departmentCode}' not found for lecturer: {$lecturer['name']}");
                $skipped++;
                continue;
            }

            // Check if lecturer already exists
            $exists = DB::table('users')
                ->where('email', $lecturer['email'])
                ->where('role_id', 2)
                ->exists();

            if (!$exists) {
                DB::table('users')->insert([
                    'role_id' => 2,
                    'department_id' => $dept->id,
                    'name' => $lecturer['name'],
                    'email' => $lecturer['email'],
                    'password' => Hash::make($lecturer['password']),
                    'email_verified_at' => now(),
                    'must_change_password' => false,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $count++;
                $this->command->info("✅ Added lecturer: " . $lecturer['name']);
            } else {
                $this->command->warn("⚠️ Lecturer already exists: " . $lecturer['name'] . " - Skipping");
                $skipped++;
            }
        }

        $this->command->info("🎉 Lecturer seeding completed!");
        $this->command->info("✅ $count lecturers added successfully!");
        if ($skipped > 0) {
            $this->command->warn("⚠️ $skipped lecturers skipped.");
        }
    }
}
