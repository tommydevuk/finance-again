<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversion>
 */
class ConversionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_transaction_id' => Transaction::factory(),
            'destination_transaction_id' => Transaction::factory(),
            'rate' => $this->faker->randomFloat(4, 0.5, 2.0),
        ];
    }
}