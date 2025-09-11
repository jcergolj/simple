<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'client_id' => \App\Models\Client::factory(),
            'description' => $this->faker->boolean(60) ? fake()->paragraph() : null,
            'hourly_rate' => $this->faker->boolean(40) ? Money::fromDecimal(
                amount: $this->faker->randomFloat(2, 30, 200),
                currency: $this->faker->randomElement([Currency::USD, Currency::EUR, Currency::GBP])
            ) : null,
        ];
    }
}
