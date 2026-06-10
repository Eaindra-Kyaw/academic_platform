<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'role_id' => 1,
            'name' => 'Admin User',
            'email' => 'admin1@mtu.edu.mm',
            'password' => Hash::make('admin001'),
            'is_active' => true,
            'must_change_password' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
