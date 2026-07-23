<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HeaderNavigationFontWeightTest extends TestCase
{
    public function test_english_navigation_uses_a_semibold_font_weight(): void
    {
        $headerTemplate = File::get(resource_path('views/components/header.blade.php'));

        $this->assertSame(6, substr_count($headerTemplate, 'text-[13px] font-semibold uppercase tracking-wide'));
        $this->assertSame(6, substr_count($headerTemplate, "'font-khmer text-lg' : 'font-semibold'"));
    }
}
