<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutMilestonesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_about_page_renders_the_animated_company_milestones_section(): void
    {
        $this->get('/about?lang=en')
            ->assertOk()
            ->assertSee('Company Milestones')
            ->assertSee('milestone-timeline', false)
            ->assertSee('timelineVisible', false)
            ->assertSee('milestone-animate', false)
            ->assertSee('milestone-timeline-progress', false);
    }
}
