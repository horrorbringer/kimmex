<?php

namespace Tests\Feature;

use App\Models\MethodologyStep;
use App\Models\Milestone;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\Partner;
use App\Models\Project;
use App\Services\HomePageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class HomePageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected HomePageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->service = app(HomePageService::class);
    }

    public function test_home_page_service_returns_fallback_arrays_when_tables_empty(): void
    {
        Cache::flush();

        $slides = $this->service->getHeroSlides('en');
        $milestonesData = $this->service->getMilestonesData('en');
        $processes = $this->service->getProcess('en');
        $projects = $this->service->getProjects('en');
        $testimonials = $this->service->getTestimonials('en');
        $news = $this->service->getNews('en');
        $partners = $this->service->getPartners('en');
        $services = $this->service->getServices('en');

        $this->assertNotEmpty($slides);
        $this->assertArrayHasKey('milestones', $milestonesData);
        $this->assertArrayHasKey('roadPath', $milestonesData);
        $this->assertNotEmpty($processes);
        $this->assertNotEmpty($projects);
        $this->assertIsArray($testimonials);
        $this->assertNotEmpty($news);
        $this->assertNotEmpty($partners);
        $this->assertNotEmpty($services);
    }

    public function test_home_page_service_returns_db_records_when_populated(): void
    {
        Cache::flush();

        Project::create([
            'isFeatured' => true,
            'isActive' => true,
            'slug' => 'test-hero-project',
            'title' => ['en' => 'Test Project EN', 'km' => 'Test Project KM'],
            'description' => ['en' => 'Description EN', 'km' => 'Description KM'],
            'location' => ['en' => 'Phnom Penh', 'km' => 'ភ្នំពេញ'],
            'status' => 'COMPLETED',
        ]);

        Milestone::create([
            'isActive' => true,
            'year' => '2026',
            'title' => ['en' => '2026 Milestone', 'km' => '2026 Milestone KM'],
            'description' => ['en' => '2026 Description', 'km' => '2026 Description KM'],
            'sortOrder' => 1,
        ]);

        MethodologyStep::create([
            'isActive' => true,
            'title' => ['en' => 'Step 1', 'km' => 'Step 1 KM'],
            'description' => ['en' => 'Step 1 Description', 'km' => 'Step 1 Description KM'],
            'orderIndex' => 1,
        ]);

        Partner::create([
            'isActive' => true,
            'name' => ['en' => 'Partner Corp', 'km' => 'Partner Corp KM'],
            'logoUrl' => 'partners/1.png',
            'orderIndex' => 1,
        ]);

        $slides = $this->service->getHeroSlides('en');
        $milestonesData = $this->service->getMilestonesData('en');
        $processes = $this->service->getProcess('en');
        $partners = $this->service->getPartners('en');

        $this->assertEquals('/projects/test-hero-project', $slides[0]['link']);
        $this->assertEquals('2026', $milestonesData['milestones'][0]['year']);
        $this->assertEquals('Step 1', $processes[0]['title']);
        $this->assertEquals('Partner Corp', $partners[0]['name']);
    }

    public function test_home_route_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('heroSlides');
        $response->assertViewHas('milestonesData');
        $response->assertViewHas('processes');
        $response->assertViewHas('projects');
        $response->assertViewHas('testimonials');
        $response->assertViewHas('allNews');
        $response->assertViewHas('partners');
    }

    public function test_home_news_filters_by_category_with_fallback_prevention(): void
    {
        Cache::flush();

        $category = NewsCategory::create([
            'name' => ['en' => 'Building & Construction', 'km' => 'សំណង់អគារ'],
            'slug' => 'news-building-construction',
            'is_active' => true,
        ]);

        NewsArticle::create([
            'isActive' => true,
            'publishedAt' => now()->subDay(),
            'slug' => 'construction-insight-1',
            'title' => ['en' => 'Construction Insight 1', 'km' => 'សំណង់ ១'],
            'news_category_id' => $category->id,
            'category' => 'Building & Construction',
        ]);

        NewsArticle::create([
            'isActive' => true,
            'publishedAt' => now()->subDays(2),
            'slug' => 'general-news-1',
            'title' => ['en' => 'General News 1', 'km' => 'ព័ត៌មានទូទៅ ១'],
            'category' => 'General',
        ]);

        // When requesting news-building-construction, returns the targeted article
        $news = $this->service->getNews('en', 'news-building-construction');
        $this->assertCount(1, $news);
        $this->assertEquals('construction-insight-1', $news[0]['id']);

        // Fallback test: when requesting a non-existent category, it falls back to active news instead of returning empty
        Cache::flush();
        $fallbackNews = $this->service->getNews('en', 'non-existent-category');
        $this->assertCount(2, $fallbackNews);
    }
}
