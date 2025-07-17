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
        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduks')->onDelete('cascade');
            $table->foreignId('rt_id')->constrained('rts')->onDelete('cascade');
            $table->integer('minggu_ke'); // Minggu ke berapa dalam tahun
            $table->year('tahun');
            $table->decimal('jumlah', 15, 2); // Jumlah kas yang harus dibayar
            $table->decimal('denda', 15, 2)->default(0); // Denda jika terlambat (2%)
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'menunggu_konfirmasi', 'lunas', 'terlambat'])->default('belum_bayar');
            $table->enum('metode_bayar', ['tunai', 'bank_transfer', 'e_wallet', 'qr_code'])->nullable();
            $table->string('bukti_bayar_file')->nullable(); // Path file bukti pembayaran
            $table->text('bukti_bayar_notes')->nullable(); // Catatan dari pembayar
            $table->timestamp('bukti_bayar_uploaded_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('confirmation_notes')->nullable(); // Catatan konfirmasi dari RT/RW
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['penduduk_id', 'tahun', 'minggu_ke']);
            $table->index(['rt_id', 'status']);
            $table->index(['status', 'tanggal_jatuh_tempo']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
