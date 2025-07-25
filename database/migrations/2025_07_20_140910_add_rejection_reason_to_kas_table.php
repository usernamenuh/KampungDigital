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
        Schema::table('kas', function (Blueprint $table) {
            if (!Schema::hasColumn('kas', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            if (Schema::hasColumn('kas', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
