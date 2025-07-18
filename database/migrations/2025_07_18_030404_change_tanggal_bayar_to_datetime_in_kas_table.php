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
            // Change column type to datetime
            // Pastikan kolom ini tidak nullable jika Anda selalu mengisinya
            // Jika sebelumnya nullable, tambahkan ->nullable()->change();
            $table->dateTime('tanggal_bayar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            // Revert column type to date if needed for rollback
            $table->date('tanggal_bayar')->nullable()->change();
        });
    }
};
