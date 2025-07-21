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
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->char('province_id', 2)->nullable();
            $table->char('regency_id', 4)->nullable();
            $table->char('district_id', 6)->nullable();
            $table->char('village_id', 10)->nullable();
            $table->string('alamat');
            $table->integer('kode_pos');
            $table->string('no_telpon')->nullable();
            $table->string('gmail');
            $table->decimal('saldo', 15, 2)->default(0.00);
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};
