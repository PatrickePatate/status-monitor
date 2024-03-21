<?php

namespace Tests\Feature;

use App\Filament\Resources\MetricResource\Pages\CreateMetric;
use App\Models\Metric;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class MetricTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase()
    {
        Artisan::call('db:seed');
    }

    /**
     * Check that services api returns a successful response and a valid json
     */
    public function test_metric_api_returns_json(): void
    {
        // creating public metric
        $service = Service::factory()->count(1)->create(['public' => true, 'show_availability' => true]);
        $metric = Metric::factory()->count(1)->create([
            'service_id' => $service->first()?->id
        ]);

        $metrics = Metric::join('services', 'metrics.service_id', '=', 'services.id')->where('services.show_availability',true)->where('services.public',true)->limit(10)->get();
        $id = $metrics->shuffle()->first();
        $response = $this->get('/api/metrics/'.$id?->id);

        $response->assertStatus(200);
        $this->assertJson($response->content());
    }

    /**
     * Check that services badge api returns a successful response and a valid svg
     */
    public function test_metric_api_does_not_return_non_public_metrics(): void
    {
        // hidden metric
        $service = Service::factory()->count(1)->create(['public' => false, 'show_availability' => false]);
        $metric = Metric::factory()->count(1)->create([
            'service_id' => $service->first()?->id
        ]);

        $metrics = Metric::join('services', 'metrics.service_id', '=', 'services.id')->where('services.show_availability',false)->where('services.public',false)->get();
        foreach($metrics as $metric){
            $response = $this->get('/api/metrics/'.$metric->id);

            $response->assertStatus(200);
            $this->assertEquals(['data' => []], $response->json());
        }

    }


    public function test_metric_can_be_created(): void
    {
        $before = Metric::count();
        $metric = Metric::create([
            'name' => fake()->name,
            'service_id' => Service::select('id')->limit(10)->get()->shuffle()->first()?->id,
            'suffix' => null,
        ]);
        $after = Metric::count();
        $this->assertEquals($before+1,$after);
        $this->assertTrue($metric->wasRecentlyCreated);
    }


    /**
     * Dashboard / Filament section
     */

    public function test_admin_can_create_metrics_via_dashboard(){
        $this->actingAs(User::first());
        Livewire::test(CreateMetric::class)
            ->fillForm([
                'name' => fake()->name,
                'service_id' => Service::select('id')->limit(10)->get()->shuffle()->first()?->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    public function test_admin_can_not_create_metrics_with_empty_name_or_empty_service_via_dashboard(){
        $this->actingAs(User::first());
        Livewire::test(CreateMetric::class)
            ->fillForm([
                'name' => null,
                'service_id' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required', 'service_id'=>'required']);
    }
}
