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

            // Relationships
            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            $table->foreignId('lecturer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('lecturer_name')->nullable();

            // Course Information
            $table->string('course_code', 20)->unique();
            $table->string('course_name', 100);

            $table->integer('credits');

            // Academic Information
            $table->string('year', 50)->nullable();

            $table->string('semester', 50)->nullable();

            $table->string('academic_year', 20)->nullable();

            // Schedule Information
            $table->string('room', 50)->nullable();

            $table->string('schedule_day', 20)->nullable();

            $table->time('schedule_time')->nullable();

            $table->time('schedule_end_time')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Soft Delete
            $table->softDeletes();

            $table->timestamps();

            // Indexes
            $table->index('course_code');
            $table->index('department_id');
            $table->index('lecturer_id');
            $table->index('year');
            $table->index('semester');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
