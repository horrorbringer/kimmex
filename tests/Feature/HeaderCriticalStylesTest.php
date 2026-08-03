<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HeaderCriticalStylesTest extends TestCase
{
    public function test_critical_header_styles_apply_when_a_loading_bar_precedes_the_header(): void
    {
        $layoutTemplate = File::get(resource_path('views/components/layouts/app.blade.php'));

        $this->assertStringContainsString('<div id="page-loading-bar" aria-hidden="true"></div>', $layoutTemplate);
        $this->assertStringContainsString('body > header { position: fixed;', $layoutTemplate);
        $this->assertStringContainsString('height: 2rem;', $layoutTemplate);
        $this->assertStringContainsString('@media (min-width: 640px)', $layoutTemplate);
        $this->assertStringNotContainsString('body > header:first-child', $layoutTemplate);
    }

    public function test_top_bar_is_removed_from_layout_after_scrolling(): void
    {
        $headerTemplate = File::get(resource_path('views/components/header.blade.php'));

        $this->assertStringContainsString("isScrolled ? 'hidden' : 'h-8 opacity-100 border-gray-100 bg-white'", $headerTemplate);
        $this->assertStringNotContainsString("isScrolled ? '!h-0", $headerTemplate);
    }
}
