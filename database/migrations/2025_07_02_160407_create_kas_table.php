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
            $table->foreignId('rw_id')->constrained('rws')->onDelete('cascade');
            $table->integer('minggu_ke');
            $table->integer('tahun');
            $table->decimal('jumlah', 12, 2); // Increased precision
            $table->date('tanggal_jatuh_tempo');
            $table->datetime('tanggal_bayar')->nullable(); // Changed to datetime
            $table->enum('status', ['belum_bayar', 'lunas', 'terlambat'])->default('belum_bayar');
            $table->enum('metode_bayar', ['tunai', 'transfer', 'digital', 'e_wallet'])->nullable(); // Added e_wallet
            $table->text('bukti_bayar')->nullable(); // Changed to text for longer content
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['penduduk_id', 'status']);
            $table->index(['rt_id', 'status']);
            $table->index(['rw_id', 'status']);
            $table->index(['tanggal_jatuh_tempo', 'status']);
            $table->index(['minggu_ke', 'tahun']);
            
            // Unique constraint to prevent duplicate kas for same period
            $table->unique(['penduduk_id', 'minggu_ke', 'tahun'], 'unique_kas_per_period');
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
