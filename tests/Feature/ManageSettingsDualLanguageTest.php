<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageSettingsDualLanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_dual_language_settings_can_be_stored_and_retrieved(): void
    {
        SystemSetting::set('organization_profile', [
            'registration_number' => 'REG-12345',
            'phone' => '+855 23 884 604',
            'email' => 'info@kimmex.com.kh',
            'en' => [
                'company_name' => 'Kimmex Construction',
                'tagline' => 'Building Cambodia\'s Future',
                'address' => '#54 St 590, Phnom Penh',
                'working_hours' => 'Mon - Fri: 8:00 AM - 5:30 PM',
            ],
            'km' => [
                'company_name' => 'ក្រុមហ៊ុន គីម ម៉ិច សំណង់',
                'tagline' => 'ការកសាង អនាគតរបស់កម្ពុជា',
                'address' => 'ផ្ទះលេខ ៥៤ ផ្លូវ ៥៩០ ភ្នំពេញ',
                'working_hours' => 'ច័ន្ទ - សុក្រ: ៨:០០ ព្រឹក - ៥:៣០ ល្ងាច',
            ],
        ]);

        $profile = SystemSetting::get('organization_profile');

        $this->assertEquals('Kimmex Construction', $profile['en']['company_name']);
        $this->assertEquals('ក្រុមហ៊ុន គីម ម៉ិច សំណង់', $profile['km']['company_name']);
        $this->assertEquals('ផ្ទះលេខ ៥៤ ផ្លូវ ៥៩០ ភ្នំពេញ', $profile['km']['address']);
    }
}
