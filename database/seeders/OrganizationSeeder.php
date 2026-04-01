<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $translator = new \App\Services\AutoTranslateService();

        $enContent = [
            'company_name' => 'KIM MEX CONSTRUCTION & INVESTMENT CO.,LTD.',
            'tagline' => 'A leading standard company in both innovation and construction.',
            'address' => '#54, St.590, Sangkat Boeung Kok II, Khan Toul Kork, Phnom Penh, Cambodia.',
            'working_hours' => 'Mon - Fri: 8:00 AM - 5:00 PM',
        ];

        // Ensure we translate the content to KM if needed
        $kmContent = $translator->translateArray($enContent, [], 'km');

        $profileData = [
            'registration_number' => '',
            'founded_date' => '',
            'phone' => '+855 23 884 604',
            'email' => 'info@kimmex.com.kh',
            'google_maps_url' => 'https://maps.google.com/maps?q=11.5756565,104.8935027&hl=en&z=15&output=embed',
            'logo' => '',
            'facebook' => '',
            'linkedin' => '',
            'youtube' => '',
            'instagram' => '',
            'telegram' => '',
            'en' => $enContent,
            'km' => $kmContent,
        ];

        \App\Models\SystemSetting::set('organization_profile', $profileData);
    }
}
