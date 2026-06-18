<?php
// database/seeders/AdminUserSeeder.php - KEEP AS IS

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin1@mtu.edu.mm'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin001'),
                'role_id' => 1,
                'email_verified_at' => now(),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        // Create Lecturer
        User::updateOrCreate(
            ['email' => 'phyothuzartun@mtu.edu.mm'],
            [
                'name' => 'Dr. Phyo Thu Zar Tun',
                'password' => Hash::make('phyo123'),
                'role_id' => 2,
                'email_verified_at' => now(),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        $this->command->info('Admin and Lecturer users created successfully!');
        $this->command->warn('Default passwords: admin001 for admin, phyo123 for lecturer');
    }
}
