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
        Schema::table('desas', function (Blueprint $table) {
            // Add foreign key for province_id
            if (Schema::hasColumn('desas', 'province_id') && Schema::hasTable('reg_provinces')) {
                $table->foreign('province_id')
                      ->references('id')
                      ->on('reg_provinces')
                      ->onDelete('set null'); // Or 'cascade' if you want to delete desas when province is deleted
            }

            // Add foreign key for regency_id
            if (Schema::hasColumn('desas', 'regency_id') && Schema::hasTable('reg_regencies')) {
                $table->foreign('regency_id')
                      ->references('id')
                      ->on('reg_regencies')
                      ->onDelete('set null');
            }

            // Add foreign key for district_id
            if (Schema::hasColumn('desas', 'district_id') && Schema::hasTable('reg_districts')) {
                $table->foreign('district_id')
                      ->references('id')
                      ->on('reg_districts')
                      ->onDelete('set null');
            }

            // Add foreign key for village_id
            if (Schema::hasColumn('desas', 'village_id') && Schema::hasTable('reg_villages')) {
                $table->foreign('village_id')
                      ->references('id')
                      ->on('reg_villages')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            // Drop foreign key for province_id
            if (Schema::hasColumn('desas', 'province_id')) {
                $table->dropForeign(['province_id']);
            }

            // Drop foreign key for regency_id
            if (Schema::hasColumn('desas', 'regency_id')) {
                $table->dropForeign(['regency_id']);
            }

            // Drop foreign key for district_id
            if (Schema::hasColumn('desas', 'district_id')) {
                $table->dropForeign(['district_id']);
            }

            // Drop foreign key for village_id
            if (Schema::hasColumn('desas', 'village_id')) {
                $table->dropForeign(['village_id']);
            }
        });
    }
};
