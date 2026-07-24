<?php

namespace Tests\Feature;

use App\Jobs\GenerateSitemap;
use App\Jobs\TranslateSystemSettings;
use App\Models\Project;
use App\Models\SystemSetting;
use App\Observers\CacheBusterObserver;
use App\Services\AutoTranslateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class AdminPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_generation_is_queued_after_project_saves(): void
    {
        Queue::fake();
        Cache::shouldReceive('forget')->zeroOrMoreTimes();

        (new CacheBusterObserver)->saved(new Project);

        Queue::assertPushed(GenerateSitemap::class);
    }

    public function test_system_setting_translation_is_queued_and_updates_the_current_settings(): void
    {
        $organizationEnglish = ['tagline' => 'Building excellence'];
        $brandEnglish = ['mission' => 'Deliver quality', 'icon' => 'flag'];

        SystemSetting::set('organization_profile', ['en' => $organizationEnglish, 'km' => []]);
        SystemSetting::set('brand_identity', ['en' => $brandEnglish, 'km' => []]);

        $translator = Mockery::mock(AutoTranslateService::class);
        $translator->shouldReceive('translateArray')
            ->once()
            ->with($organizationEnglish, [], 'km')
            ->andReturn(['tagline' => 'ការកសាងឧត្តមភាព']);
        $translator->shouldReceive('translateArray')
            ->once()
            ->with($brandEnglish, ['icon', 'image'], 'km')
            ->andReturn(['mission' => 'ផ្តល់គុណភាព', 'icon' => 'flag']);

        $job = new TranslateSystemSettings($organizationEnglish, $brandEnglish);

        $this->assertInstanceOf(ShouldQueue::class, $job);

        $job->handle($translator);

        $this->assertSame(
            ['tagline' => 'ការកសាងឧត្តមភាព'],
            SystemSetting::get('organization_profile')['km'],
        );
        $this->assertSame(
            ['mission' => 'ផ្តល់គុណភាព', 'icon' => 'flag'],
            SystemSetting::get('brand_identity')['km'],
        );
    }
}
