<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Services\NavigationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NavigationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NavigationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->service = app(NavigationService::class);
    }

    public function test_navigation_service_returns_filters_and_services(): void
    {
        Cache::flush();

        $category = ProjectCategory::create([
            'slug' => 'commercial',
            'name' => ['en' => 'Commercial', 'km' => 'ពាណិជ្ជកម្ម'],
            'isActive' => true,
        ]);

        Project::create([
            'slug' => 'test-project',
            'title' => ['en' => 'Test Project', 'km' => 'គម្រោង'],
            'description' => ['en' => 'Desc', 'km' => 'Desc'],
            'location' => ['en' => 'Phnom Penh', 'km' => 'ភ្នំពេញ'],
            'status' => ProjectStatus::COMPLETED->value,
            'isActive' => true,
            'project_category_id' => $category->id,
        ]);

        Service::create([
            'slug' => 'mep-service',
            'title' => ['en' => 'MEP Systems', 'km' => 'ប្រព័ន្ធ MEP'],
            'isActive' => true,
            'orderIndex' => 1,
        ]);

        $filters = $this->service->getNavProjectFilters('en');
        $services = $this->service->getNavServices('en');
        $footerServices = $this->service->getFooterServices('en', 1);

        $this->assertArrayHasKey('completed', $filters);
        $this->assertArrayHasKey('ongoing', $filters);
        $this->assertCount(1, $filters['completed']);
        $this->assertEquals('commercial', $filters['completed'][0]['slug']);

        $this->assertCount(1, $services);
        $this->assertEquals('mep-service', $services[0]['slug']);
        $this->assertEquals('MEP Systems', $services[0]['title']);

        $this->assertCount(1, $footerServices);
    }
}
