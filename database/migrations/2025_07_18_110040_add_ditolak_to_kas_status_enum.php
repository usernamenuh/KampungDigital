<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah kolom 'status' untuk menambahkan nilai 'ditolak'
        // Pastikan untuk menyertakan semua nilai enum yang sudah ada
        DB::statement("ALTER TABLE kas MODIFY COLUMN status ENUM('belum_bayar', 'menunggu_konfirmasi', 'lunas', 'terlambat', 'ditolak') NOT NULL DEFAULT 'belum_bayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum values if rolling back
        DB::statement("ALTER TABLE kas MODIFY COLUMN status ENUM('belum_bayar', 'menunggu_konfirmasi', 'lunas', 'terlambat') NOT NULL DEFAULT 'belum_bayar'");
    }
};
