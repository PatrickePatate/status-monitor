<?php

namespace Database\Factories;

use App\Models\Metric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MetricPoint>
 */
class MetricPointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'metric_id' => Metric::first()->id,
            'value' => $this->faker->randomNumber(2),
            'created_at' => $this->faker->dateTimeBetween('-4 days', 'now')
        ];
    }
}
