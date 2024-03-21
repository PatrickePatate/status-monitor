<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StatusPageTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase()
    {
        Artisan::call('db:seed');
    }

    /**
     * Test that the status page is working correctly
     */
    public function test_status_page_is_working(): void
    {
        $response = $this->get(route('status'));

        $response->assertStatus(200);

        $pub_services = Service::select(['id','name', 'status'])->where('public',true)->get();
        $check = [];
        foreach($pub_services as $ser){
            $check[] = $ser->name;
            $check[] = $ser->status->label();
        }
        $response->assertSeeInOrder($check);
        $response->assertViewHas('services', $pub_services);
    }
}
