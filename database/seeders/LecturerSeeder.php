<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $lecturers = [
            ['name' => 'Dr. Phyo Thu Zar Tun', 'email' => 'phyothuzartun@mtu.edu.mm', 'dept_code' => 'CS'],
            ['name' => 'Dr. Aung Ko Ko', 'email' => 'aungkoko@mtu.edu.mm', 'dept_code' => 'CS'],
            ['name' => 'Daw Thida Soe', 'email' => 'thidasoe@mtu.edu.mm', 'dept_code' => 'CS'],
            ['name' => 'Dr. Myo Myint', 'email' => 'myomyint@mtu.edu.mm', 'dept_code' => 'EC'],
            ['name' => 'U Win Zaw', 'email' => 'winzaw@mtu.edu.mm', 'dept_code' => 'EC'],
            ['name' => 'Dr. Tin Tin Hla', 'email' => 'tintinhla@mtu.edu.mm', 'dept_code' => 'EP'],
            ['name' => 'Dr. Kyaw Soe Lwin', 'email' => 'kyawsoelwin@mtu.edu.mm', 'dept_code' => 'ME'],
            ['name' => 'Dr. Thida Aung', 'email' => 'thidaaung@mtu.edu.mm', 'dept_code' => 'IT'],
            ['name' => 'U Nay Myo', 'email' => 'naymyo@mtu.edu.mm', 'dept_code' => 'IT'],
            ['name' => 'Dr. Zaw Min Naing', 'email' => 'zawminnaing@mtu.edu.mm', 'dept_code' => 'MEC'],
            ['name' => 'Dr. Hla Hla Win', 'email' => 'hlahlawin@mtu.edu.mm', 'dept_code' => 'AE'],
        ];

        foreach ($lecturers as $lec) {
            $dept = DB::table('departments')->where('code', $lec['dept_code'])->first();

            DB::table('users')->insert([
                'role_id' => 2,
                'department_id' => $dept ? $dept->id : null,
                'name' => $lec['name'],
                'email' => $lec['email'],
                'password' => Hash::make('password123'),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
