<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            // ============================================================
            // KG+12 ATTENDANCE & ROLL CALL
            // ============================================================

            $table->decimal('attendance_percentage', 5, 1)->default(0);

            $table->decimal('consistency_marks', 3, 1)->default(0.5);
            $table->decimal('punctuality_marks', 3, 1)->default(0.5);
            $table->decimal('participation_marks', 3, 1)->default(1.5);
            $table->decimal('roll_call_total', 3, 1)->default(4.0);

            $table->enum('eligibility_status', ['eligible', 'warning', 'not_eligible'])->default('not_eligible');

            $table->integer('attended_sessions')->default(0);
            $table->integer('total_sessions')->default(0);

            // ============================================================
            // RISK PREDICTION (Four Factors)
            // ============================================================

            $table->integer('consecutive_absences')->default(0);
            $table->string('attendance_trend')->default('stable');

            $table->integer('risk_score')->default(0);
            $table->string('risk_level')->default('Low');

            $table->json('risk_factors')->nullable();

            // ============================================================
            // ACADEMIC HEALTH & RECOVERY
            // ============================================================

            $table->integer('academic_health_score')->default(0);
            $table->enum('recovery_status', ['Recovering', 'Stable', 'Declining', 'Critical'])->default('Stable');

            // ============================================================
            // EVALUATION METADATA
            // ============================================================

            $table->text('evaluation_notes')->nullable();
            $table->date('evaluation_date');
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'evaluation_date'], 'unique_evaluation');

            $table->index(['student_id', 'course_id']);
            $table->index('attendance_percentage');
            $table->index('eligibility_status');
            $table->index('risk_level');
            $table->index('academic_health_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_evaluations');
    }
};
