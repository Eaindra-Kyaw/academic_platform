<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');

            // Session identifiers
            $table->string('session_token')->unique()->nullable();
            $table->string('manual_code', 10)->nullable();
            $table->string('session_code', 10)->nullable();

            // Date and time
            $table->date('session_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Periods and duration
            $table->integer('period_count')->default(4);
            $table->integer('conducted_periods')->default(4);
            $table->integer('duration')->default(30);

            // Room and mode
            $table->string('room')->nullable();
            $table->enum('qr_mode', ['session', 'semester'])->default('session');

            // Status and timestamps
            $table->enum('status', ['active', 'ended'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('qr_expires_at')->nullable();

            // Cancellation
            $table->boolean('is_cancelled')->default(false);
            $table->text('cancellation_reason')->nullable();

            // Statistics (cached for quick display)
            $table->integer('present_count')->default(0);
            $table->integer('late_count')->default(0);
            $table->integer('total_students')->default(0);

            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['lecturer_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['session_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
