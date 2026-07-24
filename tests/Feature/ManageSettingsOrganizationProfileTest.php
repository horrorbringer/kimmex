<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ManageSettingsOrganizationProfileTest extends TestCase
{
    public function test_saving_system_settings_preserves_organization_structure_configuration(): void
    {
        $settingsPage = File::get(app_path('Filament/Pages/ManageSettings.php'));

        $this->assertStringContainsString("\$existingOrganizationProfile = SystemSetting::get('organization_profile', []);", $settingsPage);
        $this->assertStringContainsString('...$existingOrganizationProfile,', $settingsPage);
        $this->assertLessThan(
            strpos($settingsPage, "'registration_number' => \$state['registration_number']"),
            strpos($settingsPage, '...$existingOrganizationProfile,'),
        );
        $this->assertStringNotContainsString("SystemSetting::get('organization_profile')['km']", $settingsPage);
    }
}
