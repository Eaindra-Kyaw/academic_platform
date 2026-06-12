<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full system access',
            ],
            [
                'name' => 'Lecturer',
                'slug' => 'lecturer',
                'description' => 'Course and attendance management',
            ],
            [
                'name' => 'Student',
                'slug' => 'student',
                'description' => 'View attendance and dashboard',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],  // Check if name exists
                [
                    'slug' => $role['slug'],
                    'description' => $role['description'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

        $this->command->info('✅ Roles seeded successfully!');
    }
}
