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
}
