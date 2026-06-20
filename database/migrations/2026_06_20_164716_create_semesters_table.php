<?php
// database/migrations/xxxx_xx_xx_create_semesters_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();

            $table->string('year_name'); // First Year, Second Year, etc.
            $table->integer('semester_number'); // 1 or 2
            $table->string('semester_name'); // First Semester, Second Semester
            $table->string('code')->unique(); // Y1S1, Y1S2, etc.
            $table->string('academic_year')->nullable(); // 2025-2026

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_current')->default(false);

            $table->timestamps();

            $table->unique(['year_name', 'semester_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
