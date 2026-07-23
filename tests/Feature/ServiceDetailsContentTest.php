<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ServiceDetailsContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_details_uses_the_summary_and_renders_the_description(): void
    {
        Queue::fake();
        Cache::forget('service_show_data_design-and-build_en');

        Service::create([
            'title' => ['en' => 'Design and Build'],
            'slug' => 'design-and-build',
            'summary' => ['en' => 'A concise service summary.'],
            'description' => ['en' => '<p>A detailed <strong>service description</strong>.</p>'],
            'isActive' => true,
        ]);

        $response = $this->get(route('services.show', ['slug' => 'design-and-build']));

        $response->assertOk()
            ->assertSeeText('A concise service summary.')
            ->assertSee('<strong>service description</strong>', false);
    }
}
