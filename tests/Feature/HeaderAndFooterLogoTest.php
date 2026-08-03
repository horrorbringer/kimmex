<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderAndFooterLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_and_footer_use_distinct_logos_with_fallback(): void
    {
        // 1. When logo_header and logo_footer are set
        SystemSetting::set('organization_profile', [
            'logo' => 'organization/default-logo.png',
            'logo_header' => 'organization/header-logo.png',
            'logo_footer' => 'organization/footer-logo.png',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Header view resolves header logo
        $headerView = view('components.header')->render();
        $this->assertStringContainsString('organization/header-logo.png', $headerView);

        // Footer view resolves footer logo
        $footerView = view('components.footer')->render();
        $this->assertStringContainsString('organization/footer-logo.png', $footerView);
    }

    public function test_header_and_footer_fall_back_to_default_logo(): void
    {
        // 2. When logo_header and logo_footer are empty, fall back to main logo
        SystemSetting::set('organization_profile', [
            'logo' => 'organization/main-logo.png',
            'logo_header' => '',
            'logo_footer' => '',
        ]);

        $headerView = view('components.header')->render();
        $this->assertStringContainsString('organization/main-logo.png', $headerView);

        $footerView = view('components.footer')->render();
        $this->assertStringContainsString('organization/main-logo.png', $footerView);
    }

    public function test_header_uses_the_lightweight_webp_logo_when_no_logo_is_configured(): void
    {
        SystemSetting::set('organization_profile', []);

        $headerView = view('components.header')->render();

        $this->assertStringContainsString('/logo.webp', $headerView);
    }
}
