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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique(); // Nomor Induk Kependudukan
            $table->foreignId('kk_id')->constrained('kks')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']); // Laki-laki, Perempuan
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Lainnya']);
            $table->enum('pendidikan', [
                'Tidak/Belum Sekolah',
                'Belum Tamat SD/Sederajat',
                'Tamat SD/Sederajat',
                'SLTP/Sederajat',
                'SLTA/Sederajat',
                'Diploma I/II',
                'Akademi/Diploma III/S.Muda',
                'Diploma IV/Strata I',
                'Strata II',
                'Strata III'
            ])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->enum('status_perkawinan', [
                'Belum Kawin',
                'Kawin',
                'Cerai Hidup',
                'Cerai Mati'
            ]);
            $table->enum('hubungan_keluarga', [
                'Kepala Keluarga',
                'Istri',
                'Anak',
                'Menantu',
                'Cucu',
                'Orangtua',
                'Mertua',
                'Famili Lain',
                'Pembantu',
                'Lainnya'
            ]);
            $table->string('kewarganegaraan')->default('WNI');
            $table->string('no_paspor')->nullable();
            $table->date('tanggal_expired_paspor')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->enum('status_penduduk', ['Tetap', 'Tidak Tetap', 'Pendatang'])->default('Tetap');
            $table->date('tanggal_pindah')->nullable();
            $table->string('alamat_sebelumnya')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif', 'meninggal', 'pindah'])->default('aktif');
            $table->date('tanggal_meninggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
