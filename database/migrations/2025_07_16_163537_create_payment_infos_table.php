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
        Schema::create('payment_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('rts')->onDelete('cascade');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('dana_number')->nullable();
            $table->string('gopay_number')->nullable();
            $table->string('ovo_number')->nullable();
            $table->string('shopeepay_number')->nullable();
            $table->string('qr_code_path')->nullable(); // Path file QR Code
            $table->text('qr_code_description')->nullable();
            $table->text('payment_notes')->nullable(); // Catatan tambahan untuk pembayaran
            $table->boolean('is_active')->default(true);
            $table->string('dana_account_name')->nullable();
            $table->string('ovo_account_name')->nullable();
            $table->string('gopay_account_name')->nullable();
            $table->string('shopeepay_account_name')->nullable();
            $table->string('qr_code_account_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_infos');
    }
};

