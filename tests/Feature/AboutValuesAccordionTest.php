<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AboutValuesAccordionTest extends TestCase
{
    public function test_about_values_accordion_opens_on_hover_with_a_short_content_transition(): void
    {
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));

        $this->assertStringContainsString('<!-- Vision / Mission / Strategy Accordion -->', $aboutPage);
        $this->assertMatchesRegularExpression('/<!-- Right: Text Content -->\s*<div>/', $aboutPage);
        $this->assertStringContainsString('<div class="space-y-4" x-data="{ active: \'vision\' }">', $aboutPage);
        $this->assertStringContainsString("? 'shadow-sm bg-white' : 'bg-gray-50'", $aboutPage);
        $this->assertStringContainsString('transition-colors duration-200 hover:border-gray-300', $aboutPage);
        $this->assertStringContainsString("@mouseenter=\"active = '{{ \$item['id'] }}'\"", $aboutPage);
        $this->assertStringContainsString('x-transition:enter="transition ease-out duration-300"', $aboutPage);
        $this->assertStringContainsString('x-transition:enter-start="opacity-0 translate-y-2"', $aboutPage);
    }
}
