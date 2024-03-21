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
        $metrics = Metric::limit(10)->get();
        return [
            'metric_id' => $metrics->shuffle()->get(0)->id,
            'value' => $this->faker->randomNumber(2),
            'created_at' => $this->faker->dateTimeBetween('-4 days', 'now')
        ];
    }
}
