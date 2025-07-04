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
            $table->foreignId('rt_id')->constrained()->onDelete('cascade');
            
            // Kas Mingguan Settings
            $table->boolean('kas_mingguan_aktif')->default(true);
            $table->decimal('kas_mingguan_jumlah', 10, 2)->default(10000);
            $table->enum('kas_mingguan_hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'])->default('minggu');
            
            // Kas Bulanan Settings
            $table->boolean('kas_bulanan_aktif')->default(false);
            $table->decimal('kas_bulanan_jumlah', 10, 2)->default(50000);
            $table->integer('kas_bulanan_tanggal')->default(1); // 1-31
            
            // Auto Generate & Reminder Settings
            $table->boolean('auto_generate')->default(true);
            $table->boolean('reminder_aktif')->default(true);
            $table->integer('reminder_hari_sebelum')->default(2);
            $table->integer('reminder_hari_setelah')->default(3);
            
            // General Settings
            $table->integer('batas_hari_terlambat')->default(7);
            $table->boolean('aktif')->default(true);
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            $table->unique('rt_id');
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
