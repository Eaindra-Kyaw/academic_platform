<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateUserStatusSeeder extends Seeder
{
    public function run()
    {
        // Get all users
        $users = User::all();

        // First, check what columns exist in the users table
        $columns = DB::getSchemaBuilder()->getColumnListing('users');
        $this->command->info("📋 Available columns: " . implode(', ', $columns));

        // Update some users to 'active' (approved)
        $approvedEmails = [
            'kayzintheint@mtu.edu.mm',
            'myothinzarswe@mtu.edu.mm',
            // Add more emails here that you want to approve
        ];

        foreach ($users as $user) {
            if (in_array($user->email, $approvedEmails)) {
                $updateData = [
                    'registration_status' => 'active',
                    'is_active' => true,
                    'updated_at' => now(),
                ];

                // Only add these fields if they exist in the table
                if (in_array('approved_at', $columns)) {
                    $updateData['approved_at'] = now();
                }
                if (in_array('approved_by', $columns)) {
                    $updateData['approved_by'] = 1;
                }

                $user->update($updateData);
                $this->command->info("✅ Approved: {$user->name} ({$user->email})");
            }
        }

        // Optionally reject some users
        $rejectedEmails = [
            // Add emails to reject here
        ];

        foreach ($users as $user) {
            if (in_array($user->email, $rejectedEmails)) {
                $updateData = [
                    'registration_status' => 'rejected',
                    'is_active' => false,
                    'updated_at' => now(),
                ];

                if (in_array('rejected_at', $columns)) {
                    $updateData['rejected_at'] = now();
                }
                if (in_array('rejected_by', $columns)) {
                    $updateData['rejected_by'] = 1;
                }
                if (in_array('rejection_reason', $columns)) {
                    $updateData['rejection_reason'] = 'Test rejection';
                }

                $user->update($updateData);
                $this->command->info("❌ Rejected: {$user->name} ({$user->email})");
            }
        }

        // Show final stats
        $pending = User::where('registration_status', 'pending')->count();
        $approved = User::where('registration_status', 'active')->count();
        $rejected = User::where('registration_status', 'rejected')->count();
        $total = User::count();

        $this->command->info("\n📊 Final Stats:");
        $this->command->info("   Pending: {$pending}");
        $this->command->info("   Approved: {$approved}");
        $this->command->info("   Rejected: {$rejected}");
        $this->command->info("   Total: {$total}");
    }
}
