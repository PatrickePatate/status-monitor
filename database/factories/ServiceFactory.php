<?php

namespace Database\Factories;

use App\Enums\ServiceStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['AVAILABLE', 'PARTIAL', 'OUTAGE', 'MAINTENANCE']),
            'created_by' => User::first()->id,
            'public' => $this->faker->boolean(),
            'show_availability' => $this->faker->boolean()
        ];
    }
}
