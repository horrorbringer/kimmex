<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomepageHeroOffsetTest extends TestCase
{
    public function test_the_homepage_hero_starts_below_the_existing_header(): void
    {
        $headerTemplate = File::get(resource_path('views/components/header.blade.php'));
        $heroTemplate = File::get(resource_path('views/components/home/hero-carousel.blade.php'));
        $layoutTemplate = File::get(resource_path('views/components/layouts/app.blade.php'));

        $this->assertStringContainsString('h-8 opacity-100 border-gray-100 bg-white', $headerTemplate);
        $this->assertStringContainsString('h-20', $headerTemplate);
        $this->assertSame(6, substr_count($headerTemplate, 'flex items-center gap-1 px-5 py-8'));
        $this->assertStringContainsString('mt-[112px] h-[calc(100dvh-112px)]', $heroTemplate);
        $this->assertStringContainsString('height: 5rem', $layoutTemplate);
    }
}
