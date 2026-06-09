<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('course_code', 20)->unique();
            $table->string('course_name', 100);
            $table->integer('credits');
            $table->integer('semester');
            $table->year('academic_year');
            $table->string('schedule_day', 20)->nullable();
            $table->time('schedule_time')->nullable();
            $table->time('schedule_end_time')->nullable();
            $table->string('room', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
