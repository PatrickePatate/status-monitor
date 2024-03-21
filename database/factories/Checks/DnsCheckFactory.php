<?php

namespace Database\Factories\Checks;

use App\Models\Metric;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Checks\DnsCheck>
 */
class DnsCheckFactory extends Factory
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
            'domain' => $this->faker->domainName(),
            'service_id' => $services->shuffle()->first()?->id,
            'ipv4_match' => $this->faker->randomElement([null, $this->faker->ipv4()]),
            'ipv6_match' => $this->faker->randomElement([null, $this->faker->ipv6()]),
            'metric_id' => $metrics->shuffle()->first()?->id
        ];
    }
}
