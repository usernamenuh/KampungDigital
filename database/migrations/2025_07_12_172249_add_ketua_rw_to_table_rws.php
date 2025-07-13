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
        Schema::table('rws', function (Blueprint $table) {
            $table->foreignId('ketua_rw_id')->nullable()->constrained('penduduks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            $table->dropForeign(['ketua_rw_id']);
            $table->dropColumn('ketua_rw_id');
        });
    }
};
