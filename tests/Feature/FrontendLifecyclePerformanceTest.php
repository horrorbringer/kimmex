<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FrontendLifecyclePerformanceTest extends TestCase
{
    public function test_navigation_cleans_up_header_and_hero_work_between_pages(): void
    {
        $header = File::get(resource_path('views/components/header.blade.php'));
        $hero = File::get(resource_path('views/components/home/hero-carousel.blade.php'));

        $this->assertStringContainsString('wire:navigate.hover', $header);
        $this->assertStringContainsString('scrollHandler: null', $header);
        $this->assertStringContainsString('resizeHandler: null', $header);
        $this->assertStringContainsString("window.removeEventListener('scroll', this.scrollHandler)", $header);
        $this->assertStringContainsString("window.removeEventListener('resize', this.resizeHandler)", $header);
        $this->assertStringContainsString('preloadTimer: null', $hero);
        $this->assertStringContainsString('transitionTimer: null', $hero);
        $this->assertStringContainsString('clearInterval(this.timer)', $hero);
        $this->assertStringContainsString('clearTimeout(this.preloadTimer)', $hero);
        $this->assertStringContainsString('clearTimeout(this.transitionTimer)', $hero);
    }
}
