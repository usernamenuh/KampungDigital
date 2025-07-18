<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kas', function (Blueprint $table) {
              DB::statement("ALTER TABLE kas MODIFY COLUMN metode_bayar ENUM('tunai', 'bank_transfer', 'e_wallet', 'qr_code', 'dana', 'ovo', 'gopay', 'shopeepay', 'bca', 'bni', 'bri', 'mandiri', 'bsi', 'cimb', 'danamon', 'permata', 'mega', 'btn', 'panin', 'maybank', 'btpn', 'commonwealth', 'uob', 'sinarmas', 'bukopin', 'jago', 'seabank', 'neo_commerce', 'allo_bank') NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            // Revert back to original enum
        DB::statement("ALTER TABLE kas MODIFY COLUMN metode_bayar ENUM('tunai', 'bank_transfer', 'e_wallet', 'qr_code') NULL");
        });
    }
};
