<?php
// database/migrations/2026_08_07_xxxxxx_add_registration_approval_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Registration & Approval Status
            $table->enum('registration_status', ['pending', 'active', 'rejected'])
                  ->default('pending')
                  ->after('is_active');

            // Timestamps
            $table->timestamp('registered_at')->nullable()->after('registration_status');
            $table->timestamp('approved_at')->nullable()->after('registered_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');

            // Rejection reason
            $table->text('rejection_reason')->nullable()->after('rejected_at');

            // Who approved/rejected (foreign keys to users table)
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('rejection_reason');

            $table->foreignId('rejected_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->after('approved_by');

            // Add indexes for faster queries
            $table->index('registration_status');
            $table->index(['registration_status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);

            // Drop columns
            $table->dropColumn([
                'registration_status',
                'registered_at',
                'approved_at',
                'rejected_at',
                'rejection_reason',
                'approved_by',
                'rejected_by',
            ]);
        });
    }
};
