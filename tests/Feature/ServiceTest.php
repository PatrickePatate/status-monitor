<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase()
    {
        Artisan::call('db:seed');
    }

    /**
     * Check that services api returns a successful response and a valid json
     */
    public function test_service_api_returns_json(): void
    {
        $response = $this->get('/api/services');

        $response->assertStatus(200);
        $response->assertJson($response->json());
    }

    /**
     * Check that services badge api returns a successful response and a valid svg
     */
    public function test_service_badge_api_returns_svg(): void
    {
        $response = $this->get('/api/services/badge/'.Service::select('id')->get()->shuffle()->first()?->id.'?type=social');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }
}
