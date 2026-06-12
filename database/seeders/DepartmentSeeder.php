<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('departments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $departments = [
            ['code' => 'CE', 'name' => 'Department of Civil Engineering', 'head_of_department' => 'To be assigned'],
            ['code' => 'ME', 'name' => 'Department of Mechanical Engineering', 'head_of_department' => 'To be assigned'],
            ['code' => 'EP', 'name' => 'Department of Electrical Power Engineering', 'head_of_department' => 'To be assigned'],
            ['code' => 'EC', 'name' => 'Department of Electronic Engineering', 'head_of_department' => 'To be assigned'],
            ['code' => 'CS', 'name' => 'Department of Computer Engineering and Information Technology', 'head_of_department' => 'To be assigned'],
            ['code' => 'MEC', 'name' => 'Department of Mechatronics Engineering', 'head_of_department' => 'To be assigned'],
            ['code' => 'CH', 'name' => 'Department of Chemical Engineering', 'head_of_department' => 'To be assigned'],
            ['code' => 'AE', 'name' => 'Department of Agricultural Engineering', 'head_of_department' => 'To be assigned'],
            ['code' => 'BT', 'name' => 'Department of Biotechnology', 'head_of_department' => 'To be assigned'],
            ['code' => 'AR', 'name' => 'Department of Architecture', 'head_of_department' => 'To be assigned'],
            ['code' => 'NT', 'name' => 'Department of Nuclear Technology', 'head_of_department' => 'To be assigned'],
        ];

        foreach ($departments as $dept) {
            // Check if department already exists
            $exists = DB::table('departments')->where('code', $dept['code'])->exists();

            if (!$exists) {
                DB::table('departments')->insert([
                    'code' => $dept['code'],
                    'name' => $dept['name'],
                    'head_of_department' => $dept['head_of_department'],
                    'description' => $dept['name'],
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
