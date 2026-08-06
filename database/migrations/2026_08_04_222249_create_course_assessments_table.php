<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('set null');
            $table->string('year')->nullable();
            $table->string('semester')->nullable();
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'active', 'closed', 'archived'])->default('draft');
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->timestamp('results_sent_at')->nullable();
            $table->timestamps();

            $table->index(['year', 'semester', 'status']);
            $table->index(['course_id', 'status']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_assessments');
    }
};
