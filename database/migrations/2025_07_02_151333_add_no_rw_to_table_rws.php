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
            $table->integer('no_rw')->nullable()->after('nama_rw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            $table->dropColumn('no_rw');
        });
    }
};
