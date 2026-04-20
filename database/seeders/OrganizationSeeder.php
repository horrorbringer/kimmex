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
            'company_name' => 'KIMMEX',
            'tagline' => 'CONSTRUCTION & INVESTMENT CO.,LTD.',
            'address' => '#54, St.590, Sangkat Boeung Kok II, Khan Toul Kork, Phnom Penh, Cambodia.',
            'working_hours' => 'Mon - Fri: 8:00 AM - 5:30 PM',
        ];

        // Ensure we translate the content to KM if needed
        $kmContent = $translator->translateArray($enContent, [], 'km');

        $profileData = [
            'registration_number' => '',
            'founded_date' => '',
            'phone' => '+855 23 884 604',
            'email' => 'info@kimmex.com.kh',
            'google_maps_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.667785689154!2d104.89350269999998!3d11.575656499999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31095176fe4b5e51%3A0x844dbeef5ee9d25b!2sKim%20mex%20Construction%20%26%20Investment%20Co.%2Cltd!5e0!3m2!1skm!2skh!4v1775701743611!5m2!1skm!2skh',
            'logo' => '',
            'facebook' => 'https://www.facebook.com/kimmex168/?locale=km_KH',
            'linkedin' => 'https://www.linkedin.com/company/kim-mex-construction-investment-co-ltd/?originalSubdomain=kh',
            'youtube' => '',
            'instagram' => '',
            'telegram' => '',
            'en' => $enContent,
            'km' => $kmContent,
        ];

        \App\Models\SystemSetting::set('organization_profile', $profileData);
    }
}
