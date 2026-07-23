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

        $this->assertStringContainsString('<x-home.trust-strip />', $homepageTemplate);
        $this->assertStringContainsString("['value' => '25+'", $trustStripTemplate);
        $this->assertStringContainsString("['value' => '150+'", $trustStripTemplate);
        $this->assertStringContainsString("['value' => 'ISO 9001'", $trustStripTemplate);
        $this->assertStringContainsString("['value' => '500+'", $trustStripTemplate);
    }
}
