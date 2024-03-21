<?php

namespace Tests\Feature\Checks;

use App\Enums\ServiceStatus;
use App\Filament\Resources\Checks\HttpCheckResource\Pages\CreateHttpCheck;
use App\Models\Checks\HttpCheck;
use App\Models\Metric;
use App\Models\Service;
use App\Models\User;
use App\Services\HttpCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use League\Uri\Http;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

class HttpCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase()
    {
        Artisan::call('db:seed');
    }


    public function test_http_checks_can_be_created(): void
    {
        $service = Service::factory()->count(1)->create()->first();

        $before = HttpCheck::count();
        $check = HttpCheck::factory()->count(1)->create(['service_id' => $service->id])->first();
        $after = HttpCheck::count();

        $this->assertEquals($before+1,$after);
        $this->assertTrue($check->wasRecentlyCreated);
    }

    public function test_http_check_on_local_ping_endpoint_end_successfully(): void
    {
        $service = Service::factory()->count(1)->create()->first();
        $metric = Metric::factory()->count(1)->create(['service_id' => $service->id])->first();
        $http_check = HttpCheck::create([
            'url' => route('ping'),
            'method' => 'get',
            'request_args' => json_encode(['query'=>'data']),
            'service_id' => $service->id,
            'http_code' => 200,
            'http_body' => "pong",
            'check_cert' => false,
            'provide_headers' => json_encode(["header"=>"header_value"]),
            'metric_id' => $metric->id
        ]);

        $this->assertEquals(ServiceStatus::AVAILABLE, (new HttpCheckService($http_check))->check());
    }

    public function test_http_check_post_method_is_sending_data(): void
    {
        // If there's is an error here, try to set default local env to "testing" in .env and down / reup sail
        // If problem still appear after that, there's maybe an issue with the function, but if problem was solved,
        // It's probably cause of sail starting the server in local env and not be able to update env while running (?)
        $service = Service::factory()->count(1)->create()->first();
        $metric = Metric::factory()->count(1)->create(['service_id' => $service->id])->first();

        $http_check = HttpCheck::create([
            'url' => route('tests.http_checks.post'),
            'method' => 'post',
            'request_args' => json_encode(['query'=>'data']),
            'service_id' => $service->id,
            'http_code' => 200,
            'http_body' => '{"query":"data"}',
            'check_cert' => false,
            'provide_headers' => json_encode(["header"=>"header_value"]),
            'metric_id' => $metric->id
        ]);
        $checker = (new HttpCheckService($http_check));
        $res = $checker->check();
        $this->assertNull($checker->failed());
        $this->assertEquals(ServiceStatus::AVAILABLE, $res);
    }

    public function test_http_check_with_wrong_body_should_fail(): void
    {
        $service = Service::factory()->count(1)->create()->first();
        $metric = Metric::factory()->count(1)->create(['service_id' => $service->id])->first();

        $http_check = HttpCheck::create([
            'url' => route('ping'),
            'method' => 'get',
            'request_args' => json_encode(['query'=>'data']),
            'service_id' => $service->id,
            'http_code' => 200,
            'http_body' => "ping pong poung",
            'check_cert' => false,
            'provide_headers' => json_encode(["header"=>"header_value"]),
            'metric_id' => $metric->id
        ]);

        // Expected value is PARTIAL because service is responding but without the good body
        $this->assertEquals(ServiceStatus::PARTIAL, (new HttpCheckService($http_check))->check());
    }

    public function test_http_check_with_wrong_http_code_should_fail(): void
    {
        $service = Service::factory()->count(1)->create()->first();
        $metric = Metric::factory()->count(1)->create(['service_id' => $service->id])->first();

        $http_check = HttpCheck::create([
            'url' => route('ping'),
            'method' => 'get',
            'request_args' => json_encode(['query'=>'data']),
            'service_id' => $service->id,
            'http_code' => 201,
            'http_body' => "pong",
            'check_cert' => false,
            'provide_headers' => json_encode(["header"=>"header_value"]),
            'metric_id' => $metric->id
        ]);

        // Expected value is PARTIAL because service is responding but without the good http code
        $this->assertEquals(ServiceStatus::PARTIAL, (new HttpCheckService($http_check))->check());
    }

    public function test_http_check_with_wrong_adress_should_fail(): void
    {
        $service = Service::factory()->count(1)->create()->first();
        $metric = Metric::factory()->count(1)->create(['service_id' => $service->id])->first();

        $http_check = HttpCheck::create([
            'url' => 'wrong-domain-impossible-to-respond.com',
            'method' => 'get',
            'request_args' => json_encode(['query'=>'data']),
            'service_id' => $service->id,
            'http_code' => 200,
            'http_body' => null,
            'check_cert' => false,
            'provide_headers' => json_encode(["header"=>"header_value"]),
            'metric_id' => $metric->id
        ]);

        // Expected value is partial because service is responding but without the good body
        $this->assertEquals(ServiceStatus::OUTAGE, (new HttpCheckService($http_check))->check());
    }

    /**
     * Dashboard / Filament section
     */

    public function test_admin_can_create_http_tests_via_dashboard(){
        $service = Service::factory()->count(1)->create()->first();
        $this->actingAs(User::first());
        Livewire::test(CreateHttpCheck::class)
            ->fillForm([
                'url' => fake()->url(),
                'method' => fake()->randomElement(['get', 'post']),
                'service_id' => $service->id,
                'http_code' => 200,
                'check_cert' => false,
                'request_args' => [],
                'provide_headers' => [],
                'metric_id' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }
}
