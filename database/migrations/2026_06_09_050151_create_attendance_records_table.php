<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->timestamp('scanned_at')->nullable();
            $table->boolean('is_manual')->default(false);  // ADDED
            $table->string('ip_address')->nullable();
            $table->text('notes')->nullable();  // ADDED
            $table->softDeletes();
            $table->timestamps();

            // Prevent duplicate attendance
            $table->unique(['attendance_session_id', 'student_id'], 'unique_session_student');
            $table->index(['attendance_session_id', 'status']);
            $table->index(['student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_records');
    }
};
