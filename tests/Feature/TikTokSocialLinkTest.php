<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TikTokSocialLinkTest extends TestCase
{
    public function test_tiktok_can_be_saved_and_displayed_with_the_organization_social_links(): void
    {
        $settingsPage = File::get(app_path('Filament/Pages/ManageSettings.php'));
        $header = File::get(resource_path('views/components/header.blade.php'));
        $footer = File::get(resource_path('views/components/footer.blade.php'));
        $contactPage = File::get(resource_path('views/pages/contact.blade.php'));
        $socialIcon = File::get(resource_path('views/components/social-icon.blade.php'));

        $this->assertStringContainsString("'tiktok' => \$org['tiktok'] ?? ''", $settingsPage);
        $this->assertStringContainsString("TextInput::make('tiktok')", $settingsPage);
        $this->assertStringContainsString("'tiktok' => \$state['tiktok']", $settingsPage);

        foreach ([$header, $footer, $contactPage] as $view) {
            $this->assertStringContainsString("\$tiktok = \$profile['tiktok']", $view);
            $this->assertStringContainsString('<x-social-icon network="tiktok"', $view);
        }

        $this->assertStringContainsString("@case('tiktok')", $socialIcon);
        $this->assertStringContainsString('<svg', $socialIcon);
    }
}
