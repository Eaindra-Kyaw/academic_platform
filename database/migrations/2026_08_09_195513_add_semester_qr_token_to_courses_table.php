<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('courses', 'semester_qr_token')) {
                $table->string('semester_qr_token', 255)->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'semester_qr_token')) {
                $table->dropColumn('semester_qr_token');
            }
        });
    }
};
