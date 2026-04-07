<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                "id" => "design-build",
                "icon" => "lucide-pen-tool",
                "title" => ["en" => "Design & Build", "kh" => "រចនា និងសាងសង់"],
                "desc" => [
                    "en" => "A seamless integration of architectural creativity and engineering precision. We handle the entire lifecycle from concept to completion.",
                    "kh" => "ការរួមបញ្ចូលគ្នារវាងភាពច្នៃប្រឌិតស្ថាបត្យកម្ម និងភាពជាក់លាក់នៃវិស្វកម្ម។ យើងគ្រប់គ្រងវដ្តជីវិតទាំងមូលពីគំនិតដល់ការបញ្ចប់។"
                ],
                "image" => "/images/projects/Thumbnail-1.jpg",
                "features" => [
                    ["en" => "Architectural Design", "kh" => "ការរចនាស្ថាបត្យកម្ម"],
                    ["en" => "Structural Engineering", "kh" => "វិស្វកម្មសំណង់"],
                    ["en" => "Permit Acquisition", "kh" => "ការស្នើសុំលិខិតអនុញ្ញាត"],
                    ["en" => "Turnkey Construction", "kh" => "សេវាកម្មសាងសង់ទាំងស្រុង"]
                ]
            ],
            [
                "id" => "construction",
                "icon" => "lucide-hammer",
                "title" => ["en" => "Construction", "kh" => "ការសាងសង់"],
                "desc" => [
                    "en" => "World-class building and civil engineering solutions. We deliver robust structures tailored to residential, commercial, and industrial needs.",
                    "kh" => "ដំណោះស្រាយវិស្វកម្មស៊ីវិល និងសំណង់កម្រិតពិភពលោក។ យើងផ្តល់ជូននូវរចនាសម្ព័ន្ធរឹងមាំតម្រូវតាមតម្រូវការលំនៅដ្ឋាន ពាណិជ្ជកម្ម និងឧស្សាហកម្ម។"
                ],
                "image" => "/images/projects/Thumbnail-4.jpg",
                "features" => [
                    ["en" => "Civil Engineering", "kh" => "វិស្វកម្មស៊ីវិល"],
                    ["en" => "Building Structure", "kh" => "រចនាសម្ព័ន្ធអគារ"],
                    ["en" => "MEP Systems", "kh" => "ប្រព័ន្ធទឹក ភ្លើង និងម៉ាស៊ីន (MEP)"],
                    ["en" => "Industrial Plants", "kh" => "រោងចក្រឧស្សាហកម្ម"]
                ]
            ],
            [
                "id" => "project-management",
                "icon" => "lucide-settings",
                "title" => ["en" => "Project Management", "kh" => "ការគ្រប់គ្រងគម្រោង"],
                "desc" => [
                    "en" => "Comprehensive oversight and strategic advisory ensuring on-time, on-budget delivery. We combine rigorous on-field management with technical and financial insights.",
                    "kh" => "ការត្រួតពិនិត្យដ៏ទូលំទូលាយ និងការផ្តល់ប្រឹក្សាយុទ្ធសាស្រ្តធានាបាននូវការដឹកជញ្ជូនទាន់ពេល និងចំថវិកា។ យើងរួមបញ្ចូលការគ្រប់គ្រងយ៉ាងតឹងរ៉ឹងជាមួយចំណេះដឹងផ្នែកបច្ចេកទេស និងហិរញ្ញវត្ថុ។"
                ],
                "image" => "/images/projects/Thumbnail-5.jpg",
                "features" => [
                    ["en" => "Cost Control & Value Engineering", "kh" => "ការគ្រប់គ្រងថ្លៃដើម និងវិស្វកម្មតម្លៃ"],
                    ["en" => "Feasibility Studies", "kh" => "ការសិក្សាសមិទ្ធភាព"],
                    ["en" => "Quality & Safety Compliance", "kh" => "ការអនុលោមតាមគុណភាព និងសុវត្ថិភាព"],
                    ["en" => "Regulatory Advice", "kh" => "ការប្រឹក្សាបទប្បញ្ញត្តិ"]
                ]
            ],
            [
                "id" => "infrastructure",
                "icon" => "lucide-truck",
                "title" => ["en" => "Infrastructure", "kh" => "ហេដ្ឋារចនាសម្ព័ន្ធ"],
                "desc" => [
                    "en" => "Developing critical infrastructure including roads, bridges, and public utilities. We build foundation for national growth.",
                    "kh" => "ការអភិវឌ្ឍហេដ្ឋារចនាសម្ព័ន្ធសំខាន់ៗរួមមាន ផ្លូវ ស្ពាន និងឧបករណ៍ប្រើប្រាស់សាធារណៈ។ យើងកសាងមូលដ្ឋានគ្រឹះសម្រាប់កំណើនជាតិ។"
                ],
                "image" => "/images/projects/Thumbnail-6.jpg",
                "features" => [
                    ["en" => "Road & Bridge Construction", "kh" => "ការសាងសង់ផ្លូវ និងស្ពាន"],
                    ["en" => "Public Utility Networks", "kh" => "បណ្តាញឧបករណ៍ប្រើប្រាស់សាធារណៈ"],
                    ["en" => "Slope Protection", "kh" => "ការការពារជម្រាល"],
                    ["en" => "Land Reclamation", "kh" => "ការចាក់ដីបំពេញ"]
                ]
            ]
        ];

        foreach ($services as $i => $s) {
            // Map features to the Repeater schema: [['name' => '...'], ...]
            // Since Service features column is not translatable, we store English names by default or handle manually if needed.
            // But usually, if we want it to show in Admin and look right, we use the name key.
            $mappedFeatures = array_map(function ($f) {
                return ['name' => $f['en']]; // Use English for the non-translatable JSON field
            }, $s['features']);

            Service::updateOrCreate(
                ['slug' => $s['id']],
                [
                    'title' => [
                        'en' => $s['title']['en'],
                        'km' => $s['title']['kh'],
                    ],
                    'description' => [
                        'en' => $s['desc']['en'],
                        'km' => $s['desc']['kh'],
                    ],
                    'summary' => [
                        'en' => Str::limit($s['desc']['en'], 150),
                        'km' => Str::limit($s['desc']['kh'], 150),
                    ],
                    'icon' => $s['icon'],
                    'image' => $s['image'],
                    'features' => $mappedFeatures,
                    'orderIndex' => $i + 1,
                    'isActive' => true,
                ]
            );
        }
    }
}
