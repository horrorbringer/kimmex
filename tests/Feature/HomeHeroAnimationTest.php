<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomeHeroAnimationTest extends TestCase
{
    public function test_the_home_hero_uses_refined_motion_timing(): void
    {
        $heroTemplate = File::get(resource_path('views/components/home/hero-carousel.blade.php'));
        $styles = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('interval: 6000', $heroTemplate);
        $this->assertStringContainsString('const duration = this.prefersReducedMotion ? 0 : 700', $heroTemplate);
        $this->assertStringContainsString('hero-kenburns 6s', $styles);
        $this->assertStringContainsString("slideDirection: 'next'", $heroTemplate);
        $this->assertStringContainsString("this.goTo((this.current + 1) % this.slides.length, 'next')", $heroTemplate);
        $this->assertStringContainsString('hero-slide-enter-right', $heroTemplate);
        $this->assertStringContainsString('hero-slide-leave-left', $heroTemplate);
        $this->assertStringContainsString('@keyframes hero-slide-in-right', $styles);
        $this->assertStringContainsString('@keyframes hero-slide-out-left', $styles);
        $this->assertSame(4, substr_count($styles, '700ms cubic-bezier(0.22, 1, 0.36, 1) both'));
        $this->assertStringContainsString('transform: translateX(100%)', $styles);
        $this->assertStringContainsString('transform: translateX(-100%)', $styles);
        $this->assertStringContainsString('scale(1.075) translate3d(0.4%, -0.3%, 0)', $styles);
        $this->assertStringContainsString('hero-content-enter > :nth-child(4)', $styles);
        $this->assertStringContainsString('transform: translateY(18px)', $styles);
        $this->assertStringContainsString('.hero-content-enter > * {', $styles);
    }
}
