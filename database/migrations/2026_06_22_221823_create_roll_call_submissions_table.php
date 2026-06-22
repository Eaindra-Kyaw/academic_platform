<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roll_call_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->integer('total_conducted_periods')->default(0);
            $table->integer('total_attended_periods')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->integer('roll_call_mark')->default(0);
            $table->enum('eligibility_status', ['eligible', 'warning', 'not_eligible'])->default('not_eligible');
            $table->enum('submission_status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->text('lecturer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'month', 'year'], 'unique_roll_call');
            $table->index(['course_id', 'month', 'year']);
            $table->index(['student_id', 'submission_status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('roll_call_submissions');
    }
};
