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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained(); // Default currency for this account
            $table->foreignId('network_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('address')->nullable(); // For crypto wallets
            $table->string('type')->default('checking'); // checking, savings, spot, margin, etc.
            $table->decimal('balance', 36, 18)->default(0); // Cached balance (optional but useful)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
