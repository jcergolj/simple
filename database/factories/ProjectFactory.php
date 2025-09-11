<?php

namespace Database\Factories;

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
            'hourly_rate' => $this->faker->boolean(40) ? new \App\ValueObjects\Money(
                amount: $this->faker->randomFloat(2, 30, 200),
                currency: $this->faker->randomElement(['USD', 'EUR', 'GBP'])
            ) : null,
        ];
    }
}
