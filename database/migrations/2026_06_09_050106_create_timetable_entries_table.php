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
            $table->string('academic_year');
            $table->string('semester');
            $table->string('year_level');
            $table->string('section')->nullable();
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->string('building')->nullable();
            $table->string('session_type')->default('lecture');
            $table->boolean('is_alternate_week')->default(false);
            $table->string('alternate_week_type')->nullable();
            $table->text('notes')->nullable();

            // ✅ Fixed: removed ->after('notes')
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['lecturer_id', 'day_of_week']);
            $table->index(['course_id', 'academic_year', 'semester']);
            $table->index(['is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable_entries');
    }
};
