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
            $table->integer('total_sessions')->default(0);
            $table->integer('attended_sessions')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->decimal('roll_call_mark', 5, 2)->default(0);
            $table->enum('eligibility_status', ['eligible', 'warning', 'not_eligible'])->default('not_eligible');
            $table->integer('consecutive_absences')->default(0);
            $table->integer('longest_absence_streak')->default(0);
            $table->enum('attendance_trend', ['improving', 'stable', 'slight_decline', 'moderate_decline', 'severe_decline'])->default('stable');
            $table->integer('sessions_needed')->default(0);
            $table->integer('evaluation_week');
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'evaluation_week'], 'att_eval_unique');        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_evaluations');
    }
};
