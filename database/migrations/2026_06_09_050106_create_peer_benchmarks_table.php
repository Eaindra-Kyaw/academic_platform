<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_benchmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->decimal('student_attendance', 5, 2);
            $table->decimal('course_avg_attendance', 5, 2);
            $table->decimal('department_avg_attendance', 5, 2);
            $table->decimal('university_avg_attendance', 5, 2);
            $table->integer('attendance_rank');
            $table->integer('total_students_in_course');
            $table->integer('student_health_score');
            $table->decimal('course_avg_health_score', 5, 2);
            $table->date('benchmark_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_benchmarks');
    }
};
