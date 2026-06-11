<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['code' => 'CE', 'name' => 'Department of Civil Engineering'],
            ['code' => 'ME', 'name' => 'Department of Mechanical Engineering'],
            ['code' => 'EP', 'name' => 'Department of Electrical Power Engineering'],
            ['code' => 'EC', 'name' => 'Department of Electronic Engineering'],
            ['code' => 'CS', 'name' => 'Department of Computer Engineering and Information Technology'],
            ['code' => 'MEC', 'name' => 'Department of Mechatronics Engineering'],
            ['code' => 'CH', 'name' => 'Department of Chemical Engineering'],
            ['code' => 'AE', 'name' => 'Department of Agricultural Engineering'],
            ['code' => 'BT', 'name' => 'Department of Biotechnology'],
            ['code' => 'AR', 'name' => 'Department of Architecture'],
            ['code' => 'NT', 'name' => 'Department of Nuclear Technology'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert([
                'code' => $dept['code'],
                'name' => $dept['name'],
                'head_of_department' => 'To be assigned',
                'description' => $dept['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
