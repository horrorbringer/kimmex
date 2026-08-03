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
        $this->assertStringContainsString('preloadImage(src, srcset = null)', $hero);
        $this->assertStringContainsString('this.preloadImage(slide?.image, slide?.srcset)', $hero);
    }

    public function test_public_pages_avoid_duplicate_prefetching_and_eager_load_hero_categories(): void
    {
        $layout = File::get(resource_path('views/components/layouts/app.blade.php'));
        $hero = File::get(resource_path('views/components/home/hero-carousel.blade.php'));
        $projects = File::get(resource_path('views/components/home/projects.blade.php'));

        $this->assertStringContainsString(':wght@400;500;600;700;800;900', $layout);
        $this->assertStringNotContainsString('instant.page', $layout);
        $this->assertStringContainsString("->with('projectCategory')", $hero);
        $this->assertStringContainsString('optimizedLocalImageUrl($project[\'image\'])', $projects);
        $this->assertStringContainsString('cloudinaryResponsiveSrcset($project[\'image\'], [640, 960, 1440])', $projects);
        $this->assertStringContainsString('sizes="(min-width: 1024px) 50vw, 100vw"', $projects);
    }
}
