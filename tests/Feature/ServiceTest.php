<?php

namespace Tests\Feature;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
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
        //creating public service
        $service = Service::factory()->count(1)->create(['public' => true, 'show_availability' => true]);

        $response = $this->get('/api/services');

        $response->assertStatus(200);
        $this->assertJson($response->content());
    }

    /**
     * Check that services badge api returns a successful response and a valid svg
     */
    public function test_service_badge_api_returns_svg(): void
    {
        //creating  public service
        $service = Service::factory()->count(1)->create(['public' => true, 'show_availability' => true]);

        $id = Service::where('public',true)->select('id')->get()->shuffle()->first()?->id;
        $response = $this->get('/api/services/badge/'.$id.'?type=social');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_service_api_does_not_return_non_public_services(){
        $response = $this->get(route('api.services'));

        foreach ($response->json() as $service){
            $this->assertTrue($service['public']);
        }
    }
    public function test_service_can_be_created(): void
    {
        $before = Service::count();
        $service = Service::create([
            'name' => fake()->name,
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(ServiceStatus::class),
            'created_by' => User::limit(10)->get()->shuffle()->first()?->id,
            'public' => fake()->boolean(),
            'show_availability' => fake()->boolean(),
        ]);
        $after = Service::count();
        $this->assertEquals($before+1,$after);
        $this->assertTrue($service->wasRecentlyCreated);
    }


    /**
     * Dashboard / Filament section
     */

    public function test_admin_can_create_services_via_dashboard(){
        $this->actingAs(User::first());
        Livewire::test(CreateService::class)
            ->fillForm([
                'name' => fake()->name,
                'description' => fake()->sentence(),
                'public' => fake()->boolean(),
                'show_availability' => fake()->boolean(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    public function test_admin_can_not_create_services_with_empty_name_via_dashboard(){
        $this->actingAs(User::first());
        Livewire::test(CreateService::class)
            ->fillForm([
                'name' => null,
                'description' => fake()->sentence(),
                'public' => fake()->boolean(),
                'show_availability' => fake()->boolean(),
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }
}
