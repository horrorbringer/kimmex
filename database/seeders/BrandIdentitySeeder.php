<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandIdentitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translator = new \App\Services\AutoTranslateService();

        $enContent = [
            'company_story' => "Established in 1999, Kim Mex Construction & Investment Co., Ltd. is a deeply respected and duly registered enterprise in the Kingdom of Cambodia. Over more than 25 years, we have evolved from a specialized engineering firm into a premier multi-disciplinary construction partner, delivering iconic infrastructure that stands the test of time while elevating the communities we serve.",
            'ceo_message' => "Construction is not just about concrete and steel. It is about building trust, fostering communities, and leaving a legacy that stands the test of time.",
            'mission' => "To bridge the gap between concept and reality through exceptional precision, military-grade safety, and sustainable building practices.",
            'vision' => "To be the most trusted and innovative construction partner in Cambodia, setting the benchmark for excellence in architecture and engineering.",
            'goal' => "To maintain long-term leadership in the Cambodian market through talent development, CMS investment, and a zero-accident safety records.",
            'values_list' => [
                ['title' => 'HONESTY', 'description' => 'Unwavering transparency and ethics in every contract and communication.', 'icon' => 'lucide-shield'],
                ['title' => 'DISCIPLINE', 'description' => 'Military-grade precision and adherence to strict project timelines.', 'icon' => 'lucide-award'],
                ['title' => 'QUALITY FIRST', 'description' => 'Rigorous international standards applied from foundation to finish.', 'icon' => 'lucide-check-circle-2'],
                ['title' => 'CULTURE OF SHARING', 'description' => 'Fostering knowledge exchange to empower our local workforce.', 'icon' => 'lucide-share-2'],
                ['title' => 'CREATIVITY & INNOVATION', 'description' => 'Pioneering new construction techniques and modern architectural designs.', 'icon' => 'lucide-lightbulb'],
                ['title' => 'RESPECT THE VALUE OF PEOPLE', 'description' => 'Putting the safety and dignity of our staff at the heart of our operations.', 'icon' => 'lucide-users'],
                ['title' => 'SENSE OF OWNERSHIP', 'description' => 'Every team member takes personal pride and responsibility for every brick laid.', 'icon' => 'lucide-key'],
            ],
        ];

        // Auto-translate to Khmer, skipping the 'icon' keys
        $kmContent = $translator->translateArray($enContent, ['icon'], 'km');

        $brandData = [
            'ceo_name' => 'Okhna. TOUCH KIM',
            'en' => $enContent,
            'km' => $kmContent,
        ];

        \App\Models\SystemSetting::set('brand_identity', $brandData);
    }
}
