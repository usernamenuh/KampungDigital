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
        // Update the enum to include 'proposal' and other missing categories
        DB::statement("ALTER TABLE notifikasis MODIFY COLUMN kategori ENUM('kas', 'sistem', 'pengumuman', 'reminder', 'proposal', 'user', 'payment') DEFAULT 'sistem'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE notifikasis MODIFY COLUMN kategori ENUM('kas', 'sistem', 'pengumuman', 'reminder') DEFAULT 'sistem'");
    }
};
