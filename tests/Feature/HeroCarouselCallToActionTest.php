<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HeroCarouselCallToActionTest extends TestCase
{
    public function test_the_hero_uses_compact_call_to_action_button_styles(): void
    {
        $heroTemplate = File::get(resource_path('views/components/home/hero-carousel.blade.php'));

        $this->assertStringContainsString('VIEW PROJECT', $heroTemplate);
        $this->assertStringContainsString('CONTACT US', $heroTemplate);
        $this->assertStringContainsString('px-5 sm:px-6 lg:px-7 py-2.5 sm:py-3', $heroTemplate);
        $this->assertStringContainsString('text-[9px] sm:text-[10px]', $heroTemplate);
        $this->assertStringContainsString('font-khmer text-xs sm:text-sm tracking-normal', $heroTemplate);
        $this->assertSame(2, substr_count($heroTemplate, 'self-start'));
        $this->assertStringContainsString('h-[100dvh] min-h-[580px] sm:min-h-[640px]', $heroTemplate);
        $this->assertStringContainsString('mt-2 sm:mt-3 flex flex-row flex-wrap', $heroTemplate);
    }
}
