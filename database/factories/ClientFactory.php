<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'hourly_rate' => $this->faker->boolean(70) ? Money::fromDecimal(
                amount: $this->faker->randomFloat(2, 25, 150),
                currency: $this->faker->randomElement([Currency::USD, Currency::EUR, Currency::GBP])
            ) : null,
        ];
    }
}
