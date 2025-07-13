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
            $table->foreignId('kepala_desa_id')->nullable()->constrained('penduduks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->dropForeign(['kepala_desa_id']);
            $table->dropColumn('kepala_desa_id');
        });
    }
};
