<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Get Computer Engineering department ID
        $dept = DB::table('departments')->where('code', 'CS')->first();

        DB::table('users')->insert([
            'role_id' => 3,
            'department_id' => $dept ? $dept->id : null,
            'name' => 'Eaindra Kyaw',
            'email' => 'eaindrakyaw@mtu.edu.mm',
            'password' => Hash::make('eain123'),
            'current_year' => 3,
            'enrollment_year' => 2022,
            'is_active' => true,
            'must_change_password' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
