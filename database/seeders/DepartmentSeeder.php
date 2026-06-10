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
            ['code' => 'CS', 'name' => 'Department of Computer Engineering and Information Technology'],
            ['code' => 'EC', 'name' => 'Department of Electronic Engineering'],
            ['code' => 'EP', 'name' => 'Department of Electrical Power Engineering'],
            ['code' => 'ME', 'name' => 'Department of Mechanical Engineering'],
            ['code' => 'CE', 'name' => 'Department of Civil Engineering'],
            ['code' => 'MEC', 'name' => 'Department of Mechatronics Engineering'],
            ['code' => 'CH', 'name' => 'Department of Chemical Engineering'],
            ['code' => 'NT', 'name' => 'Department of Nuclear Technology'],
            ['code' => 'AE', 'name' => 'Department of Agricultural Engineering'],
            ['code' => 'BT', 'name' => 'Department of Biotechnology'],
            ['code' => 'AR', 'name' => 'Department of Architecture (B.Arch.)'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert([
                'code' => $dept['code'],
                'name' => $dept['name'],
                'description' => $dept['name'],
                'head_of_department' => 'To be assigned',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
