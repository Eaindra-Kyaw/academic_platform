<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            // Student enrolled in a course
            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->onDelete('cascade');

            // Enrollment details
            $table->date('enrollment_date');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'dropped'
            ])->default('pending');

            // === ADD THESE 3 REQUIRED COLUMNS ===
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->decimal('roll_call_mark', 5, 2)->default(0);
            $table->enum('eligibility_status', ['eligible', 'warning', 'not_eligible'])->default('not_eligible');
            // ===================================

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('dropped_at')->nullable();

            $table->timestamps();

            // Prevent duplicate enrollment
            $table->unique(['student_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
