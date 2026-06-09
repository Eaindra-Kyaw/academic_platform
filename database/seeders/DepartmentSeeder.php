<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'CS', 'name' => 'Computer Engineering and Information Technology', 'head' => 'Dr. Phyo Thu Zar Tun'],
            ['code' => 'EC', 'name' => 'Electronic Engineering', 'head' => 'Dr. Myo Myint'],
            ['code' => 'EP', 'name' => 'Electrical Power Engineering', 'head' => 'Dr. Tin Tin Hla'],
            ['code' => 'ME', 'name' => 'Mechanical Engineering', 'head' => 'Dr. Kyaw Soe Lwin'],
            ['code' => 'CE', 'name' => 'Civil Engineering', 'head' => 'Dr. Aung Kyaw Myint'],
            ['code' => 'Archi', 'name' => 'Architectural Engineering', 'head' => 'Dr. Thida Aung'],
            ['code' => 'MEC', 'name' => 'Mechatronic Engineering', 'head' => 'Dr. Zaw Min Naing'],
            ['code' => 'AE', 'name' => 'Agricultural Engineering', 'head' => 'Dr. Hla Hla Win'],
        ];

        foreach ($departments as $dept) {
            // Check if department already exists
            $exists = DB::table('departments')->where('code', $dept['code'])->exists();

            if (!$exists) {
                DB::table('departments')->insert([
                    'code' => $dept['code'],
                    'name' => $dept['name'],
                    'head_of_department' => $dept['head'],
                    'description' => $dept['name'] . ' Department',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $this->command->info("Added department: " . $dept['name']);
            } else {
                $this->command->warn("Department already exists: " . $dept['name'] . " - Skipping");
            }
        }

        $this->command->info("Department seeding completed!");
    }
}
