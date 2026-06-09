<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();

            // User Information
            $table->string('student_id', 50)->nullable()->unique();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Profile
            $table->string('profile_picture', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();

            // Academic Information
            $table->integer('semester')->nullable();
            $table->year('enrollment_year')->nullable();

            // Account Status
            $table->boolean('is_active')->default(true);

            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('role_id');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
