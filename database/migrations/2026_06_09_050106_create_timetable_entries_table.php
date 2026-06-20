<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('academic_year'); // e.g., "2025-2026"
            $table->string('semester'); // e.g., "First Semester", "Second Semester"
            $table->string('year_level'); // e.g., "First Year", "Second Year"
            $table->string('section')->nullable(); // e.g., "A", "B"
            $table->string('day_of_week'); // Monday, Tuesday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->string('building')->nullable();
            $table->string('session_type')->default('lecture'); // lecture, tutorial, lab, etc.
            $table->boolean('is_alternate_week')->default(false);
            $table->string('alternate_week_type')->nullable(); // "A", "B"
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lecturer_id', 'day_of_week']);
            $table->index(['course_id', 'academic_year', 'semester']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable_entries');
    }
};
