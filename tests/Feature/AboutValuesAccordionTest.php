<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AboutValuesAccordionTest extends TestCase
{
    public function test_who_we_are_section_renders_cleanly_without_unnecessary_accordeons(): void
    {
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));

        $this->assertStringContainsString('<!-- === WHO WE ARE === -->', $aboutPage);
        $this->assertMatchesRegularExpression('/<!-- Right: Text Content -->\s*<div>/', $aboutPage);
        $this->assertStringContainsString("{{ __('WHO WE ARE') }}", $aboutPage);
        $this->assertStringNotContainsString('@mouseenter', $aboutPage);
        $this->assertStringNotContainsString('@mouseleave', $aboutPage);
        $this->assertStringNotContainsString('md:absolute md:top-full', $aboutPage);
    }
}
