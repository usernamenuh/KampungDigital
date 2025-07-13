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
         Schema::create('pengaturan_kas', function (Blueprint $table) {
            $table->id();
            $table->decimal('jumlah_kas_mingguan', 15, 2); // Jumlah kas per minggu
            $table->decimal('persentase_denda', 5, 2)->default(2.00); // Persentase denda (default 2%)
            $table->integer('batas_hari_pembayaran')->default(7); // Berapa hari setelah minggu berakhir
            $table->integer('hari_peringatan')->default(1); // Berapa hari sebelum jatuh tempo kirim peringatan
            $table->boolean('auto_generate_weekly')->default(false); // Otomatis generate kas mingguan
            $table->text('pesan_peringatan')->nullable(); // Template pesan peringatan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_kas');
    }
};
