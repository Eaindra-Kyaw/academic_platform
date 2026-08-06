<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('course_assessments')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->text('question_text');
            $table->enum('type', ['rating', 'text'])->default('rating');
            $table->integer('min_rating')->nullable()->default(1);
            $table->integer('max_rating')->nullable()->default(5);
            $table->timestamps();

            $table->index(['assessment_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_questions');
    }
};
