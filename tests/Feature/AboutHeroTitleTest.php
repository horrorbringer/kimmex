<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AboutHeroTitleTest extends TestCase
{
    public function test_about_hero_heading_keeps_building_white(): void
    {
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));

        $this->assertStringContainsString("{{ __('BUILDING') }}", $aboutPage);
        $this->assertStringContainsString('!text-white', $aboutPage);
        $this->assertStringContainsString('style="color: #FFFFFF !important;"', $aboutPage);
        $this->assertStringContainsString('style="color: var(--primary-color, #E31E24) !important;"', $aboutPage);
        $this->assertStringContainsString(':priority-image="$aboutHeroImageUrl"', $aboutPage);
        $this->assertStringContainsString('cloudinaryResponsiveSrcset($aboutHeroImageUrl, [640, 960, 1440])', $aboutPage);
        $this->assertStringContainsString('cloudinaryResponsiveSrcset($image, [320, 640, 960])', $aboutPage);
        $this->assertStringContainsString('sizes="100vw"', $aboutPage);
    }
}
