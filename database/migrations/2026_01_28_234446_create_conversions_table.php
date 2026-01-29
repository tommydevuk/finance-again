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
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('destination_transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->decimal('rate', 36, 18); // Exchange rate (Dest / Source)
            $table->timestamps();
            
            // Ensure a transaction can only be part of one conversion leg (optional but good for integrity)
            $table->unique('source_transaction_id');
            $table->unique('destination_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};
