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
        Schema::create('saldo_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rt_id')->nullable();
            $table->unsignedBigInteger('rw_id')->nullable();
            $table->unsignedBigInteger('desa_id')->nullable();
            $table->unsignedBigInteger('kas_id')->nullable();
            $table->string('transaction_type'); // income, expense, transfer_to_rw, transfer_from_rt, reversal, adjustment, kas_transfer
            $table->decimal('amount', 15, 0); // Amount of transaction (positive for income, negative for expense)
            $table->decimal('previous_saldo', 15, 0)->default(0); // Balance before transaction
            $table->decimal('new_saldo', 15, 0)->default(0); // Balance after transaction
            $table->text('description')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // User who processed the transaction
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('rt_id')->references('id')->on('rts')->onDelete('cascade');
            $table->foreign('rw_id')->references('id')->on('rws')->onDelete('cascade');
            $table->foreign('desa_id')->references('id')->on('desas')->onDelete('cascade');
            $table->foreign('kas_id')->references('id')->on('kas')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['rt_id', 'created_at']);
            $table->index(['rw_id', 'created_at']);
            $table->index(['transaction_type', 'created_at']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_transactions');
    }
};
