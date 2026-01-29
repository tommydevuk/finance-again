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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained();
            $table->foreignId('network_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index(); // deposit, withdrawal, spend, trade_buy, trade_sell, bet_win, bet_loss
            $table->decimal('amount', 36, 18); // Positive for income/wins, negative for spend/losses
            $table->decimal('amount_native', 36, 18)->nullable(); // Value in account's native currency (if different)
            $table->decimal('fee', 36, 18)->default(0); // Transaction fee
            $table->foreignId('fee_currency_id')->nullable()->constrained('currencies')->nullOnDelete(); // Currency of the fee
            $table->timestamp('date')->index();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->json('meta_data')->nullable();
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->string('external_id')->nullable();
            $table->string('status')->default('completed')->index(); // pending, completed, failed
            $table->timestamps();

            $table->index(['account_id', 'date']); // Composite index for common history queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
