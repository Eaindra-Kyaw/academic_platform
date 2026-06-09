<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('university_analytics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->onDelete('cascade');

            $table->integer('total_students')->default(0);
            $table->integer('total_lecturers')->default(0);
            $table->integer('total_courses')->default(0);

            $table->decimal('attendance_rate', 5, 2)->default(0);

            $table->integer('students_at_risk')->default(0);

            $table->decimal('eligibility_rate', 5, 2)->default(0);

            $table->decimal('avg_academic_health_score', 5, 2)->default(0);

            $table->integer('active_sessions')->default(0);

            $table->string('busiest_classroom', 50)->nullable();

            $table->integer('busiest_classroom_count')->nullable();

            $table->json('weekly_engagement')->nullable();

            $table->date('analytics_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('university_analytics');
    }
};
