<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackPageView;
use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrackPageViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_it_marks_a_public_page_view_unknown_when_the_local_geo_ip_database_is_unavailable(): void
    {
        Route::middleware('web')->get('/analytics-performance-test', fn () => response('<title>Performance test</title>'));

        $this->withServerVariables([
            'REMOTE_ADDR' => '8.8.8.8',
            'HTTP_USER_AGENT' => 'Performance test browser',
        ])->get('/analytics-performance-test')
            ->assertOk();

        $this->assertDatabaseHas(PageView::class, [
            'path' => '/analytics-performance-test',
            'ip' => '8.8.8.8',
            'country' => 'Unknown',
        ]);
    }

    public function test_it_defers_analytics_until_after_the_response_is_ready(): void
    {
        $middleware = app(TrackPageView::class);
        $request = Request::create('/analytics-termination-test', 'GET', server: [
            'REMOTE_ADDR' => '8.8.8.8',
            'HTTP_USER_AGENT' => 'Performance test browser',
        ]);
        $response = new Response('<title>Performance test</title>');

        $handledResponse = $middleware->handle($request, fn (Request $request): Response => $response);

        $this->assertSame($response, $handledResponse);
        $this->assertDatabaseCount(PageView::class, 0);

        $middleware->terminate($request, $response);

        $this->assertDatabaseHas(PageView::class, [
            'path' => '/analytics-termination-test',
            'ip' => '8.8.8.8',
        ]);
    }

    public function test_it_counts_one_view_for_repeated_page_loads_from_the_same_visitor_within_thirty_minutes(): void
    {
        Route::middleware('web')->get('/analytics-deduplication-test', fn () => response('<title>Deduplication test</title>'));

        $request = [
            'REMOTE_ADDR' => '8.8.8.8',
            'HTTP_USER_AGENT' => 'Performance test browser',
        ];

        $this->withServerVariables($request)->get('/analytics-deduplication-test')->assertOk();
        $this->withServerVariables($request)->get('/analytics-deduplication-test')->assertOk();

        $this->assertSame(1, PageView::where('path', '/analytics-deduplication-test')->count());
    }
}
