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
        // Delete existing admin1 if exists
        DB::table('users')->where('email', 'admin1@mtu.edu')->delete();

        // Insert new admin1
        DB::table('users')->insert([
            'role_id' => 1,
            'name' => 'Admin User',
            'email' => 'admin1@mtu.edu',
            'password' => Hash::make('admin001'),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info('Admin user created: admin1@mtu.edu / admin001');
    }
}
