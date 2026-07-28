<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AboutValuesAccordionTest extends TestCase
{
    public function test_about_values_accordion_expands_on_hover_and_collapses_when_unhovered(): void
    {
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));

        $this->assertStringContainsString('<!-- Vision / Mission / Strategy Accordion -->', $aboutPage);
        $this->assertMatchesRegularExpression('/<!-- Right: Text Content -->\s*<div>/', $aboutPage);
        $this->assertStringContainsString('<div class="space-y-4" x-data="{ active: null }">', $aboutPage);
        $this->assertStringContainsString("? 'z-20 bg-white shadow-lg shadow-titan-navy/10' : 'bg-gray-50'", $aboutPage);
        $this->assertStringContainsString('transition-colors duration-200 hover:border-gray-300', $aboutPage);
        $this->assertStringContainsString("@mouseenter=\"active = '{{ \$item['id'] }}'\"", $aboutPage);
        $this->assertStringContainsString('@mouseleave="active = null"', $aboutPage);
        $this->assertStringContainsString('overflow-visible', $aboutPage);
        $this->assertStringContainsString('md:absolute md:top-full md:left-0 md:right-0', $aboutPage);
        $this->assertStringContainsString('x-transition:enter="transition ease-out duration-300"', $aboutPage);
        $this->assertStringContainsString('x-transition:enter-start="opacity-0 translate-y-2"', $aboutPage);
    }
}
