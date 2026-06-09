<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('qr_code')->nullable();
            $table->timestamp('qr_expiry')->nullable();
            $table->string('session_token', 255)->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->integer('total_students')->default(0);
            $table->integer('present_count')->default(0);
            $table->integer('absent_count')->default(0);
            $table->integer('late_count')->default(0);
            $table->string('room', 50)->nullable();
            $table->enum('status', ['active','closed','cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
