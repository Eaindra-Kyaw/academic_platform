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

            // Add these columns if they don't exist in your original migration
            $table->string('session_token')->unique()->nullable();
            $table->string('manual_code', 10)->nullable();
            $table->string('room')->nullable();
            $table->integer('duration')->default(30);

            $table->enum('status', ['active', 'ended'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Add index for faster queries
            $table->index(['lecturer_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
