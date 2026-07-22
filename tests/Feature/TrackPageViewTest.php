<?php

namespace Tests\Feature;

use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrackPageViewTest extends TestCase
{
    use RefreshDatabase;

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
}
