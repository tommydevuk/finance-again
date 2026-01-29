<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => \App\Models\Account::factory(),
            'type' => $this->faker->randomElement(['deposit', 'withdrawal', 'spend']),
            'amount' => $this->faker->randomFloat(8, -1000, 1000),
            'currency_id' => \App\Models\Currency::factory(),
            'network_id' => \App\Models\Network::factory(),
            'fee' => $this->faker->randomFloat(8, 0, 10),
            'fee_currency_id' => null, // Default to same currency, or random currency if needed
            'amount_native' => $this->faker->randomFloat(8, -1000, 1000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->word(),
            'meta_data' => ['block_number' => $this->faker->numberBetween(1000, 1000000), 'gas_fee' => $this->faker->randomFloat(8, 0, 0.1)],
            'external_id' => $this->faker->uuid(),
            'status' => 'completed',
        ];
    }
}
