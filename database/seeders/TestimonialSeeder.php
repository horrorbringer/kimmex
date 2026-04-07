<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'clientName' => ['en' => 'Sok V.', 'km' => 'សុខ វ.'],
                'clientRole' => ['en' => 'CEO, Alpha Corp', 'km' => 'នាយកប្រតិបត្តិ Alpha Corp'],
                'content' => [
                    'en' => 'They delivered our commercial building ahead of schedule and with impeccable quality.',
                    'km' => 'ពួកគេបានប្រគល់អគារពាណិជ្ជកម្មរបស់យើងមុនកាលវិភាគ និងមានគុណភាពឥតខ្ចោះ។'
                ],
                'rating' => 5,
                'isFeatured' => true,
                'orderIndex' => 1,
            ],
            [
                'clientName' => ['en' => 'Dr. Cham', 'km' => 'បណ្ឌិត ចំ'],
                'clientRole' => ['en' => 'Director of Infrastructure', 'km' => 'ប្រធានផ្នែកហេដ្ឋារចនាសម្ព័ន្ធ'],
                'content' => [
                    'en' => 'The attention to detail and safety standards were outstanding during the water plant project.',
                    'km' => 'ការយកចិត្តទុកដាក់លើព័ត៌មានលម្អិត និងស្តង់ដារសុវត្ថិភាពគឺលេចធ្លោយ៉ាងខ្លាំងក្នុងអំឡុងពេលគម្រោងរោងចក្រទឹក។'
                ],
                'rating' => 5,
                'isFeatured' => true,
                'orderIndex' => 2,
            ],
            [
                'clientName' => ['en' => 'Mr. Rithy', 'km' => 'លោក រិទ្ធី'],
                'clientRole' => ['en' => 'Property Developer', 'km' => 'អ្នកអភិវឌ្ឍន៍អចលនទ្រព្យ'],
                'content' => [
                    'en' => 'Highly professional team. They handled all the MEP complexities without a single delay.',
                    'km' => 'ក្រុមការងារដែលមានវិជ្ជាជីវៈខ្ពស់។ ពួកគេបានដោះស្រាយរាល់ភាពស្មុគស្មាញនៃ MEP ដោយមិនមានការយឺតយ៉ាវសូម្បីតែម្តង។'
                ],
                'rating' => 5,
                'isFeatured' => true,
                'orderIndex' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(['clientName->en' => $testimonial['clientName']['en']], $testimonial);
        }
    }
}
