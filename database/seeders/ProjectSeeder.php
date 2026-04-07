<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Enums\ProjectStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = ProjectCategory::all()->pluck('id', 'slug')->toArray();

        $projects = [
            [
                'title' => [
                    'en' => 'Ministry of Economy & Finance Building Expansion',
                    'km' => 'ការពង្រីកអគារក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ'
                ],
                'slug' => 'ministry-of-economy-finance-building-expansion',
                'location' => [
                    'en' => 'Phnom Penh, Cambodia',
                    'km' => 'ភ្នំពេញ, កម្ពុជា'
                ],
                'status' => ProjectStatus::COMPLETED,
                'completionDate' => '2026-10-01',
                'client' => 'MEF',
                'scale' => '50,000 SQM',
                'timeline' => '2023 - 2026',
                'heroImage' => '/images/projects/Thumbnail-1.jpg',
                'background' => [
                    'en' => 'A definitive case study on administrative centralization and public infrastructure integration for the Royal Government of Cambodia.',
                    'km' => 'ការសិក្សាករណីជាក់លាក់ស្តីពីមជ្ឈការរដ្ឋបាល និងសមាហរណកម្មហេដ្ឋារចនាសម្ព័ន្ធសាធារណៈសម្រាប់រាជរដ្ឋាភិបាលកម្ពុជា។'
                ],
                'objectives' => [
                    'en' => 'To deliver a state-of-the-art office complex with Grade A specifications, ensuring maximum energy efficiency and seamless integration of governmental systems.',
                    'km' => 'ដើម្បីផ្តល់នូវអគារការិយាល័យទំនើបបំផុតដែលមានលក្ខណៈបច្ចេកទេសថ្នាក់ A ដែលធានានូវប្រសិទ្ធភាពថាមពលអតិបរមា និងការរួមបញ្ចូលយ៉ាងរលូននៃប្រព័ន្ធរដ្ឋាភិបាល។'
                ],
                'designConcept' => [
                    'en' => '<p>The architectural design focuses on a <strong>"Solid Foundation"</strong> theme, utilizing heavy reinforced concrete with a glass facade that symbolizes transparency and strength.</p>',
                    'km' => '<p>ការរចនាស្ថាបត្យកម្មផ្តោតលើប្រធានបទ <strong>"គ្រឹះរឹងមាំ"</strong> ដោយប្រើប្រាស់បេតុងអាមេធ្ងន់ៗជាមួយនឹងមុខកញ្ចក់ដែលតំណាងឱ្យតម្លាភាព និងភាពខ្លាំង។</p>'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>General Contracting</li><li>Structural Engineering</li><li>MEP Systems Integration</li><li>Interior Fit-out</li></ul>',
                    'km' => '<ul><li>ការម៉ៅការទូទៅ</li><li>វិស្វកម្មរចនាសម្ព័ន្ធ</li><li>ការរួមបញ្ចូលប្រព័ន្ធ MEP</li><li>ការរៀបចំផ្ទៃក្នុង</li></ul>'
                ],
                'engineeringNarrative' => [
                    'en' => '<p><strong>Challenge:</strong> Strict government security protocols.<br><strong>Solution:</strong> Developed a specialized vetting and access control system.</p>',
                    'km' => '<p><strong>បញ្ហាប្រឈម៖</strong> ពិធីសារសុវត្ថិភាពរដ្ឋាភិបាលតឹងរ៉ឹង។<br><strong>ដំណោះស្រាយ៖</strong> បានបង្កើតប្រព័ន្ធត្រួតពិនិត្យ និងគ្រប់គ្រងការចូលប្រើប្រាស់ជំនាញ។</p>'
                ],
                'project_category_id' => $categoryMap['government'] ?? null,
                'isFeatured' => true,
            ],
            [
                'title' => [
                    'en' => 'National Bank HQ',
                    'km' => 'ទីស្នាក់ការកណ្តាលធនាគារជាតិ'
                ],
                'slug' => 'national-bank-hq',
                'location' => [
                    'en' => 'Phnom Penh, Cambodia',
                    'km' => 'ភ្នំពេញ, កម្ពុជា'
                ],
                'status' => ProjectStatus::COMPLETED,
                'completionDate' => '2025-01-01',
                'client' => 'National Bank of Cambodia',
                'scale' => '40,000 SQM',
                'timeline' => '2022 - 2025',
                'heroImage' => '/images/projects/Thumbnail-5.jpg',
                'background' => [
                    'en' => 'Landmark project for the financial district.',
                    'km' => 'គម្រោងដ៏សំខាន់សម្រាប់តំបន់ហិរញ្ញវត្ថុ។'
                ],
                'scopeContributions' => [
                    'en' => '<p>General Contracting and Construction Management.</p>',
                    'km' => '<p>ការម៉ៅការទូទៅ និងការគ្រប់គ្រងការសាងសង់។</p>'
                ],
                'project_category_id' => $categoryMap['government'] ?? null,
                'isFeatured' => true,
            ],
            [
                'title' => [
                    'en' => 'Khleang Toeuk WTP',
                    'km' => 'រោងចក្រប្រព្រឹត្តិកម្មទឹកស្អាតឃ្លាំងទឹក'
                ],
                'slug' => 'khleang-toeuk-wtp',
                'location' => [
                    'en' => 'Phnom Penh, Cambodia',
                    'km' => 'ភ្នំពេញ, កម្ពុជា'
                ],
                'status' => ProjectStatus::ONGOING,
                'completionDate' => '2027-01-01',
                'client' => 'PPWSA',
                'scale' => 'Large Scale',
                'timeline' => '2024 - 2027',
                'heroImage' => '/images/projects/Thumbnail-2.jpg',
                'background' => [
                    'en' => 'Expanding water access across the capital.',
                    'km' => 'ពង្រីកលទ្ធភាពទទួលបានទឹកស្អាតនៅទូទាំងរាជធានី។'
                ],
                'scopeContributions' => [
                    'en' => '<p>Infrastructure Engineering and Treatment System Integration.</p>',
                    'km' => '<p>វិស្វកម្មហេដ្ឋារចនាសម្ព័ន្ធ និងការរួមបញ្ចូលប្រព័ន្ធប្រព្រឹត្តិកម្ម។</p>'
                ],
                'project_category_id' => $categoryMap['infrastructure'] ?? null,
                'isFeatured' => true,
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::updateOrCreate(
                ['slug' => $projectData['slug']],
                $projectData
            );

            // Seed some more images for the first one
            if ($project->slug === 'ministry-of-economy-finance-building-expansion') {
                ProjectImage::updateOrCreate(
                    ['url' => '/images/projects/Thumbnail-2.jpg', 'projectId' => $project->id],
                    ['caption' => 'Side View']
                );
                ProjectImage::updateOrCreate(
                    ['url' => '/images/projects/Thumbnail-3.jpg', 'projectId' => $project->id],
                    ['caption' => 'Interior']
                );
            }
        }
    }
}
