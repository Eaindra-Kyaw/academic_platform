<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_calendar', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('event_name');
            $table->enum('type', ['holiday', 'university_closure', 'public_holiday', 'special_event']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('date');
            $table->index('date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_calendar');
    }
};
