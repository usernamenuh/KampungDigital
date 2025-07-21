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
        Schema::table('users', function (Blueprint $table) {
             // Check if the column exists before modifying to prevent errors on fresh installs
            if (Schema::hasColumn('users', 'status')) {
                // Modify the existing 'status' column to include 'pending_verification'
                DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive', 'pending_verification') DEFAULT 'pending_verification'");
            } else {
                // If 'status' column does not exist, add it with the correct enum values
                $table->enum('status', ['active', 'inactive', 'pending_verification'])->default('pending_verification')->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                // Revert to the previous ENUM values if needed
                DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
            }
        });
    }
};
