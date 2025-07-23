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
        Schema::create('bantuan_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rws')->onDelete('cascade');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade'); // User RW yang mengajukan
            $table->string('judul_proposal');
            $table->text('deskripsi');
            $table->decimal('jumlah_bantuan', 15, 0); // Jumlah bantuan yang diminta
            $table->string('file_proposal')->nullable(); // Path ke file proposal
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Kades yang review
            $table->timestamp('reviewed_at')->nullable();
            $table->text('catatan_review')->nullable(); // Catatan dari kades
            $table->decimal('jumlah_disetujui', 15, 0)->nullable(); // Jumlah yang disetujui (bisa beda dari yang diminta)
            $table->timestamp('tanggal_pencairan')->nullable(); // Kapan bantuan dicairkan
            $table->timestamps();

            // Indexes
            $table->index(['rw_id', 'status']);
            $table->index('status');
            $table->index('submitted_by');
            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan_proposals');
    }
};
