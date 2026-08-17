<?php

namespace Tests\Feature;

use App\Models\MethodologyStep;
use App\Models\Sector;
use App\Models\Service;
use App\Services\ServicesPageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ServicesPageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Queue::fake();
    }

    public function test_services_page_loads_successfully_via_controller(): void
    {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertViewIs('pages.services');
        $response->assertViewHasAll(['services', 'process', 'sectors', 'lang', 'processGridColsClass']);
    }

    public function test_services_page_service_returns_fallbacks_when_db_empty(): void
    {
        $service = app(ServicesPageService::class);

        $services = $service->getServices();
        $process = $service->getProcess();
        $sectors = $service->getSectors('en');

        $this->assertNotEmpty($services);
        $this->assertCount(4, $services);
        $this->assertCount(5, $process);
        $this->assertCount(4, $sectors);
    }

    public function test_services_page_service_returns_db_records_when_populated(): void
    {
        Service::create([
            'slug' => 'custom-mep-service',
            'title' => ['en' => 'Custom MEP Service', 'km' => 'សេវាកម្ម MEP ពិសេស'],
            'description' => ['en' => 'High quality MEP engineering.', 'km' => 'វិស្វកម្ម MEP គុណភាពខ្ពស់។'],
            'icon' => 'lucide-zap',
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $service = app(ServicesPageService::class);
        $services = $service->getServices();

        $this->assertCount(1, $services);
        $this->assertEquals('custom-mep-service', $services[0]['id']);
        $this->assertEquals('Custom MEP Service', $services[0]['title']['en']);
    }

    public function test_service_observer_busts_cache_on_save_and_delete(): void
    {
        $service = app(ServicesPageService::class);
        $service->getServices();

        $this->assertTrue(Cache::has('services_index_data'));

        $record = Service::create([
            'slug' => 'observer-test-service',
            'title' => ['en' => 'Observer Test', 'km' => 'តេស្ត'],
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $this->assertFalse(Cache::has('services_index_data'));

        $service->getServices();
        $this->assertTrue(Cache::has('services_index_data'));

        $record->delete();
        $this->assertFalse(Cache::has('services_index_data'));
    }

    public function test_methodology_step_observer_busts_cache(): void
    {
        $service = app(ServicesPageService::class);
        $service->getProcess();

        $this->assertTrue(Cache::has('services_process_array_'.app()->getLocale()));

        $step = MethodologyStep::create([
            'title' => ['en' => 'Step 1 Analysis', 'km' => 'ជំហានទី១'],
            'description' => ['en' => 'Initial analysis', 'km' => 'ការវិភាគ'],
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $this->assertFalse(Cache::has('services_process_array_'.app()->getLocale()));
    }

    public function test_sector_observer_busts_cache(): void
    {
        $service = app(ServicesPageService::class);
        $service->getSectors('en');

        $this->assertTrue(Cache::has('services_sectors_array_en'));

        $sector = Sector::create([
            'title' => ['en' => 'Mining', 'km' => 'រ៉ែ'],
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $this->assertFalse(Cache::has('services_sectors_array_en'));
    }

    public function test_process_grid_cols_class_mapping(): void
    {
        $service = app(ServicesPageService::class);

        $this->assertEquals('lg:grid-cols-1', $service->getProcessGridColsClass(1));
        $this->assertEquals('lg:grid-cols-2', $service->getProcessGridColsClass(2));
        $this->assertEquals('lg:grid-cols-3', $service->getProcessGridColsClass(3));
        $this->assertEquals('lg:grid-cols-4', $service->getProcessGridColsClass(4));
        $this->assertEquals('lg:grid-cols-5', $service->getProcessGridColsClass(5));
        $this->assertEquals('lg:grid-cols-5', $service->getProcessGridColsClass(8));
    }
}
