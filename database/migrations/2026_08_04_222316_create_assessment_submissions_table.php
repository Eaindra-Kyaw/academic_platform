<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('course_assessments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('answers')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique(['assessment_id', 'student_id', 'course_id'], 'unique_assessment_submission');
            $table->index(['assessment_id', 'student_id']);
            $table->index(['course_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_submissions');
    }
};
