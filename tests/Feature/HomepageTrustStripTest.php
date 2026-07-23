<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomepageTrustStripTest extends TestCase
{
    public function test_the_homepage_includes_a_compact_company_highlights_strip(): void
    {
        $homepageTemplate = File::get(resource_path('views/welcome.blade.php'));
        $trustStripTemplate = File::get(resource_path('views/components/home/trust-strip.blade.php'));
        $ctaTemplate = File::get(resource_path('views/components/home/cta.blade.php'));
        $styles = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('<x-home.trust-strip />', $homepageTemplate);
        $this->assertStringContainsString("['value' => 25", $trustStripTemplate);
        $this->assertStringContainsString("['value' => 150", $trustStripTemplate);
        $this->assertStringContainsString("['value' => 'ISO 9001'", $trustStripTemplate);
        $this->assertStringContainsString("['value' => 500", $trustStripTemplate);
        $this->assertStringContainsString('x-intersect.once="count()"', $trustStripTemplate);
        $this->assertStringContainsString('home-cta-sheen', $ctaTemplate);
        $this->assertStringContainsString('flex flex-row gap-2 sm:gap-4 shrink-0', $ctaTemplate);
        $this->assertStringContainsString('@keyframes home-cta-glow', $styles);
    }
}
