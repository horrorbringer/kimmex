<?php

namespace Tests\Feature;

use App\Enums\JobPostingStatus;
use App\Enums\ProjectStatus;
use App\Filament\Widgets\StatsOverview;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\PageView;
use App\Models\Project;
use App\Models\Subscriber;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use ReflectionMethod;
use Tests\TestCase;

class DashboardStatsOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_are_cached_after_the_initial_aggregate_query(): void
    {
        Queue::fake();

        $now = CarbonImmutable::now()->startOfDay()->addHours(9);
        CarbonImmutable::setTestNow($now);

        $job = JobPosting::create([
            'title' => ['en' => 'Engineer'],
            'slug' => 'engineer',
            'status' => JobPostingStatus::OPEN,
        ]);

        Inquiry::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Hello',
            'is_read' => false,
        ]);
        JobApplication::create([
            'jobId' => $job->id,
            'applicantName' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'resumeUrl' => 'resumes/john.pdf',
        ]);
        PageView::create([
            'url' => 'https://example.com',
            'path' => '/',
            'visited_at' => $now,
        ]);
        Project::create([
            'title' => ['en' => 'Project'],
            'slug' => 'project',
            'category' => 'Commercial',
            'status' => ProjectStatus::ONGOING,
        ]);
        Subscriber::create([
            'email' => 'subscriber@example.com',
            'is_active' => true,
            'unsubscribe_token' => 'subscriber-token',
        ]);

        Cache::forget('admin_dashboard_stats');
        $queryCount = 0;
        DB::listen(function () use (&$queryCount): void {
            $queryCount++;
        });

        $method = new ReflectionMethod(StatsOverview::class, 'getStats');
        $method->invoke(new StatsOverview);
        $firstRenderQueryCount = $queryCount;

        $method->invoke(new StatsOverview);
        $secondRenderQueryCount = $queryCount - $firstRenderQueryCount;

        $this->assertLessThanOrEqual(10, $firstRenderQueryCount);
        $this->assertLessThanOrEqual(2, $secondRenderQueryCount);
    }
}
