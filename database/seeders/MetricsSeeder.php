<?php

namespace Database\Seeders;

use App\Models\Metric;
use App\Models\MetricPoint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Metric::factory()->count(4)->create();
        MetricPoint::factory()->count(200)->create();
    }
}
