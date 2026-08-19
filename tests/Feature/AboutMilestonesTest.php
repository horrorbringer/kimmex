<?php

namespace Tests\Feature;

use App\Models\Milestone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AboutMilestonesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_about_page_renders_the_animated_company_milestones_section(): void
    {
        Milestone::create([
            'year' => '2020',
            'title' => ['en' => 'Project turning point'],
            'description' => ['en' => '<p>A major achievement.</p>'],
            'sortOrder' => 1,
            'isActive' => true,
            'isFeatured' => true,
        ]);
        Cache::flush();

        $this->get('/about?lang=en')
            ->assertOk()
            ->assertSee('Company Milestones')
            ->assertSee('OUR JOURNEY')
            ->assertSee('kimmexCanvasJsChart');
    }
}
