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
        Schema::create('attendance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            // Attendance Metrics
            $table->integer('attendance_percentage')->default(0);
            $table->integer('roll_call_score')->default(0); // 0-10
            $table->enum('eligibility_status', ['Eligible', 'Warning', 'Not Eligible'])->default('Not Eligible');

            // Session Stats
            $table->integer('sessions_attended')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->integer('consecutive_absences')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);

            // Risk Metrics (for Day 22-24)
            $table->integer('risk_score')->default(0); // 0-100
            $table->string('risk_level')->default('Low'); // Low, Medium, High

            // Academic Health Score (for Day 25)
            $table->integer('academic_health_score')->default(0); // 0-100

            // Recovery Status
            $table->enum('recovery_status', ['Recovering', 'Stable', 'Declining', 'Critical'])->default('Stable');

            // Evaluation Details
            $table->text('evaluation_notes')->nullable();
            $table->date('evaluation_date');
            $table->timestamps();

            // Unique constraint: one evaluation per student per course per day
            $table->unique(['student_id', 'course_id', 'evaluation_date'], 'unique_evaluation');

            // Indexes
            $table->index(['student_id', 'course_id']);
            $table->index('attendance_percentage');
            $table->index('eligibility_status');
            $table->index('risk_level');
            $table->index('academic_health_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_evaluations');
    }
};
