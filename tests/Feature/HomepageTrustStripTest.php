<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomepageTrustStripTest extends TestCase
{
    public function test_the_homepage_displays_impact_statistics_only_in_the_strip_below_the_hero(): void
    {
        $homepageTemplate = File::get(resource_path('views/welcome.blade.php'));
        $heroTemplate = File::get(resource_path('views/components/home/hero-carousel.blade.php'));
        $ctaTemplate = File::get(resource_path('views/components/home/cta.blade.php'));
        $styles = File::get(resource_path('css/app.css'));

        $trustStripTemplate = File::get(resource_path('views/components/home/trust-strip.blade.php'));

        $this->assertStringContainsString('<x-home.trust-strip />', $homepageTemplate);
        $this->assertStringNotContainsString('<div class="text-2xl font-black text-white">25+</div>', $heroTemplate);
        $this->assertStringContainsString("['value' => 25", $trustStripTemplate);
        $this->assertStringContainsString("['value' => 150", $trustStripTemplate);
        $this->assertStringContainsString("['value' => 500", $trustStripTemplate);
        $this->assertStringContainsString('<x-page-view-count :total="true" :count-only="true"', $trustStripTemplate);
        $this->assertStringContainsString('home-cta-sheen', $ctaTemplate);
        $this->assertStringContainsString('flex flex-row gap-2 sm:gap-4 shrink-0', $ctaTemplate);
        $this->assertStringContainsString('@keyframes home-cta-glow', $styles);
    }
}
