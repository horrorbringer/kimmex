<?php

namespace Database\Seeders;

use App\Models\MethodologyStep;
use Illuminate\Database\Seeder;

class MethodologySeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'icon' => 'lucide-users',
                'title' => ['en' => 'Consultation & Analysis', 'kh' => 'ការពិគ្រោះយោបល់ និងការវិភាគ'],
                'description' => ['en' => 'Understanding requirements, performing site data deep dives, and feasibility analysis.', 'kh' => 'ការស្វែងយល់ពីតម្រូវការ និងការវិភាគលទ្ធភាព។'],
                'orderIndex' => 1,
            ],
            [
                'icon' => 'lucide-layout-dashboard',
                'title' => ['en' => 'Planning & Procurement', 'kh' => 'ការធ្វើផែនការ និងលទ្ធកម្ម'],
                'description' => ['en' => 'Defining project roadmap, budgets, baselines, and vendor selection.', 'kh' => 'ការកំណត់ផែនទីបង្ហាញផ្លូវ ថវិកា និងការជ្រើសរើសអ្នកផ្គត់ផ្គង់។'],
                'orderIndex' => 2,
            ],
            [
                'icon' => 'lucide-hard-hat',
                'title' => ['en' => 'Execution & Advisory', 'kh' => 'ការអនុវត្ត និងការប្រឹក្សា'],
                'description' => ['en' => 'On-site management, daily coordination, and ongoing strategic guidance.', 'kh' => 'ការគ្រប់គ្រងការដ្ឋាន និងការសម្របសម្រួលប្រចាំថ្ងៃ។'],
                'orderIndex' => 3,
            ],
            [
                'icon' => 'lucide-settings',
                'title' => ['en' => 'Systems Integration', 'kh' => 'ការធ្វើសមាហរណកម្មប្រព័ន្ធ'],
                'description' => ['en' => 'Implementing smart building tech, MEP systems, and advanced automation.', 'kh' => 'ការអនុវត្តបច្គេកវិទ្យាអាគារឆ្លាតវៃ និងប្រព័ន្ធ MEP។'],
                'orderIndex' => 4,
            ],
            [
                'icon' => 'lucide-check-circle-2',
                'title' => ['en' => 'Close-out & Reporting', 'kh' => 'ការបញ្ចប់ និងការរាយការណ៍'],
                'description' => ['en' => 'Final accounting, documentation, and delivering actionable recommendations.', 'kh' => 'ការរៀបចំឯកសារចុងក្រោយ និងរបាយការណ៍។'],
                'orderIndex' => 5,
            ],
        ];

        foreach ($steps as $step) {
            MethodologyStep::create($step);
        }
    }
}
