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
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', ['info', 'warning', 'success', 'error'])->default('info');
            $table->enum('kategori', ['kas', 'sistem', 'pengumuman', 'reminder'])->default('sistem');
            $table->json('data')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'dibaca']);
            $table->index(['tipe', 'kategori']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
