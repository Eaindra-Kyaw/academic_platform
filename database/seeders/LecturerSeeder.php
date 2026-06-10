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
        DB::table('users')->insert([
            'role_id' => 2,
            'name' => 'Dr. Phyo Thu Zar Tun',
            'email' => 'phyothuzartun@mtu.edu.mm',
            'password' => Hash::make('phyo123'),
            'is_active' => true,
            'must_change_password' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
