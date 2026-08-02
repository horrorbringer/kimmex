<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MobileInputFontSizeTest extends TestCase
{
    public function test_frontend_form_controls_use_a_non_zooming_font_size_on_mobile(): void
    {
        $styles = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('@media (max-width: 767px)', $styles);
        $this->assertStringContainsString('input:not([type="checkbox"]):not([type="radio"]):not([type="range"])', $styles);
        $this->assertStringContainsString("select,\n  textarea", $styles);
        $this->assertStringContainsString('font-size: 16px;', $styles);
    }
}
