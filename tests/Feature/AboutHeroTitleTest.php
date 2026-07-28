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
    }
}
