<?php

namespace Database\Factories\Checks;

use App\Models\Metric;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Checks\HttpCheck>
 */
class HttpCheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $services = Service::limit(10)->get();
        $metrics = Metric::limit(10)->get();
        return [
            'url' => $this->faker->url(),
            'method' => $this->faker->randomElement(['get', 'post']),
            'request_args' => json_encode([]),
            'service_id' => $services->shuffle()->first()?->id,
            'http_code' => $this->faker->randomElement([200, 200, 200, 500, 401]),
            'http_body' => '',
            'check_cert' => $this->faker->boolean(),
            'provide_headers' => json_encode([]),
            'metric_id' => $metrics->shuffle()->first()?->id
        ];
    }
}
