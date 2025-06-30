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
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rws')->onDelete('cascade');
            $table->string('nama_rt');
            $table->string('alamat')->nullable();
            $table->string('ketua_rt')->nullable();
            $table->string('no_telpon')->nullable();
            $table->integer('jumlah_kk')->default(0);
            $table->decimal('saldo', 15, 2)->default(0.00);
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rts');
    }
};
