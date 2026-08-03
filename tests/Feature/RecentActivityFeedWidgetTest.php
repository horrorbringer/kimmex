<?php

namespace Tests\Feature;

use App\Filament\Widgets\RecentActivityFeedWidget;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RecentActivityFeedWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_caches_the_recent_activity_feed_for_one_minute(): void
    {
        Queue::fake();
        $cacheKey = 'admin_dashboard_recent_activity_'.app()->getLocale();
        Cache::forget($cacheKey);

        $inquiry = Inquiry::query()->create([
            'name' => 'Vanny',
            'email' => 'vanny@example.com',
            'subject' => 'Website inquiry',
            'message' => 'Hello',
        ]);

        $widget = app(RecentActivityFeedWidget::class);

        $firstActivities = $widget->getActivities();

        $inquiry->delete();

        $secondActivities = $widget->getActivities();

        $this->assertTrue(Cache::has($cacheKey));
        $this->assertCount($firstActivities->count(), $secondActivities);
        $this->assertSame('Vanny', $secondActivities->first()['title']);
    }
}
