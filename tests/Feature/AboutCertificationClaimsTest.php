<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AboutCertificationClaimsTest extends TestCase
{
    public function test_about_page_does_not_claim_iso_certification(): void
    {
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));
        $homeAbout = File::get(resource_path('views/components/home/about.blade.php'));
        $trustStrip = File::get(resource_path('views/components/home/trust-strip.blade.php'));
        $servicesPage = File::get(resource_path('views/pages/services.blade.php'));
        $khmerTranslations = File::get(lang_path('km.json'));

        $this->assertStringContainsString("__('Quality Assurance')", $aboutPage);
        $this->assertStringContainsString(__('Rigorous QA/QC procedures'), $aboutPage);
        $this->assertStringContainsString('<x-page-view-count :total="true" :count-only="true"', $aboutPage);
        $this->assertStringNotContainsString('ISO 9001', $aboutPage);
        $this->assertStringNotContainsString('9001:2015 Certified', $aboutPage);
        $this->assertStringNotContainsString('ISO', $homeAbout);
        $this->assertStringNotContainsString('ISO', $trustStrip);
        $this->assertStringNotContainsString('ISO', $servicesPage);
        $this->assertStringNotContainsString('ISO', $khmerTranslations);
    }
}
