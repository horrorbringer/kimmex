<?php

namespace Tests\Feature\View\Components;

use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_the_cached_view_count_for_a_path(): void
    {
        PageView::create([
            'url' => 'https://example.test/news/performance-update',
            'path' => '/news/performance-update',
            'visited_at' => now(),
            'country' => 'Unknown',
        ]);
        PageView::create([
            'url' => 'https://example.test/news/performance-update',
            'path' => '/news/performance-update',
            'visited_at' => now(),
            'country' => 'Unknown',
        ]);

        $this->blade('<x-page-view-count path="/news/performance-update" />')
            ->assertSee('2 views');
    }
}
