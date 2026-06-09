<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_health_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('attendance_percentage_score');
            $table->integer('roll_call_score');
            $table->integer('attendance_streak_score');
            $table->integer('engagement_trend_score');
            $table->integer('academic_health_score');
            $table->enum('health_category', ['excellent', 'stable', 'at_risk', 'critical']);
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->enum('recovery_status', ['recovering', 'stable', 'declining', 'critical'])->default('stable');
            $table->integer('calculation_week');
            $table->timestamps();

            $table->unique(['student_id', 'calculation_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_health_scores');
    }
};
