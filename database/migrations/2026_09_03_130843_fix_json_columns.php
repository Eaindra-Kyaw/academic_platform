<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Fix university_analytics table
        if (Schema::hasTable('university_analytics')) {
            Schema::table('university_analytics', function (Blueprint $table) {
                $table->text('weekly_engagement')->nullable()->change();
            });
        }

        // Fix assessment_submissions table
        if (Schema::hasTable('assessment_submissions')) {
            Schema::table('assessment_submissions', function (Blueprint $table) {
                $table->text('answers')->nullable()->change();
            });
        }

        // Fix attendance_evaluations table
        if (Schema::hasTable('attendance_evaluations')) {
            Schema::table('attendance_evaluations', function (Blueprint $table) {
                $table->text('risk_factors')->nullable()->change();
            });
        }
    }

    public function down()
    {
        // Revert changes if needed
    }
};
