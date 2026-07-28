<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomeImpactStatsTest extends TestCase
{
    public function test_home_hero_keeps_statistics_in_the_strip_below_the_hero(): void
    {
        $hero = File::get(resource_path('views/components/home/hero-carousel.blade.php'));

        $this->assertStringNotContainsString('<div class="text-2xl font-black text-white">25+</div>', $hero);
        $this->assertStringNotContainsString('<x-page-view-count path="/" light :count-only="true"', $hero);
    }
}
