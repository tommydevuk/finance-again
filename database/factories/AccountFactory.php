<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entity_id' => \App\Models\Entity::factory(),
            'platform_id' => \App\Models\Platform::factory(),
            'currency_id' => \App\Models\Currency::factory(),
            'network_id' => \App\Models\Network::factory(),
            'name' => $this->faker->word().' Account',
            'address' => $this->faker->uuid(),
            'type' => $this->faker->randomElement(['checking', 'savings', 'spot']),
            'balance' => $this->faker->randomFloat(8, 0, 10000),
        ];
    }
}
