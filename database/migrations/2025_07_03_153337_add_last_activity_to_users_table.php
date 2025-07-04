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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_activity')) {
                $table->timestamp('last_activity')->nullable()->after('remember_token');
                $table->index('last_activity');
            }
            
            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('last_activity');
                $table->index('is_online');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_activity')) {
                $table->dropIndex(['last_activity']);
                $table->dropColumn('last_activity');
            }
            
            if (Schema::hasColumn('users', 'is_online')) {
                $table->dropIndex(['is_online']);
                $table->dropColumn('is_online');
            }
        });
    }
};
