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
            $table->string('room')->nullable();
            $table->integer('day_of_week'); // 1=Monday, 7=Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->string('lecturer_name')->nullable();
            $table->string('academic_year')->nullable();
            $table->string('semester')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable_entries');
    }
};
