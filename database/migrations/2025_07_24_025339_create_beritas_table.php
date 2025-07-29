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
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten');
            $table->string('gambar')->nullable();
            $table->string('video')->nullable();
            $table->string('link')->nullable(); // Link eksternal opsional
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('kategori', ['umum', 'pengumuman', 'kegiatan', 'pembangunan', 'kesehatan', 'pendidikan', 'ekonomi', 'sosial', 'lingkungan', 'keamanan'])->default('umum');
            $table->enum('tingkat_akses', ['rt', 'rw', 'desa']); // rt = RT tertentu, rw = seluruh RT dalam RW, desa = seluruh desa
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Penulis berita
            $table->foreignId('rt_id')->nullable()->constrained()->onDelete('cascade'); // Jika berita khusus RT
            $table->foreignId('rw_id')->nullable()->constrained()->onDelete('cascade'); // Jika berita khusus RW
            $table->timestamp('tanggal_publish')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_pinned')->default(false); // Untuk berita penting
            $table->text('excerpt')->nullable(); // Ringkasan berita
            $table->json('tags')->nullable(); // Tags untuk kategorisasi
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'tanggal_publish']);
            $table->index(['tingkat_akses', 'rt_id', 'rw_id']);
            $table->index(['kategori', 'status']);
            $table->index(['is_pinned', 'tanggal_publish']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
