<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->integer('risk_score');
            $table->enum('risk_level', ['low_risk', 'medium_risk', 'high_risk']);
            $table->integer('consecutive_absences')->default(0);
            $table->enum('attendance_trend', ['improving','stable','slight_decline','moderate_decline','severe_decline'])->default('stable');
            $table->integer('attendance_risk_points');
            $table->integer('roll_call_risk_points');
            $table->integer('absence_risk_points');
            $table->integer('trend_risk_points');
            $table->text('risk_explanation')->nullable();
            $table->date('prediction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_predictions');
    }
};
