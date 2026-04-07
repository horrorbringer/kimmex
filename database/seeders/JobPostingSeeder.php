<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobPosting;
use Illuminate\Support\Str;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'title' => ['en' => 'Senior Project Manager', 'km' => 'អ្នកគ្រប់គ្រងគម្រោងជាន់ខ្ពស់'],
                'slug' => 'senior-project-manager',
                'location' => ['en' => 'Phnom Penh', 'km' => 'ភ្នំពេញ'],
                'type' => 'FULL_TIME',
                'summary' => ['en' => 'Leading large-scale construction projects from inception to completion.', 'km' => 'ដឹកនាំគម្រោងសាងសង់ខ្នាតធំចាប់ពីការចាប់ផ្តើមរហូតដល់ការបញ្ចប់។'],
                'experience' => ['en' => '5-10 Years', 'km' => '៥-១០ ឆ្នាំ'],
                'salary' => ['en' => 'Competitive', 'km' => 'ប្រកួតប្រជែង'],
                'responsibilities' => ['en' => "Manage project budgets and timelines.\nCoordinate with subcontractors and engineers.\nEnsure safety and quality compliance.", 'km' => "គ្រប់គ្រងថវិកានិងពេលវេលាគម្រោង។\nសម្របសម្រួលជាមួយអ្នកម៉ៅការបន្តនិងវិស្វករ។\nធានាបាននូវការអនុលោមតាមសុវត្ថិភាពនិងគុណភាព។"],
                'requirements' => ['en' => "Bachelor's degree in Civil Engineering.\nStrong leadership and communication skills.\nProficiency in project management software.", 'km' => "បរិញ្ញាបត្រផ្នែកវិស្វកម្មស៊ីវិល។\nជំនាញដឹកនាំនិងទំនាក់ទំនងខ្លាំង។\nជំនាញក្នុងកម្មវិធីគ្រប់គ្រងគម្រោង។"],
                'benefits' => ['en' => "Health insurance.\nPerformance bonuses.\nProfessional development opportunities.", 'km' => "ធានារ៉ាប់រងសុខភាព។\nប្រាក់រង្វាន់ការងារ។\nឱកាសអភិវឌ្ឍន៍វិជ្ជាជីវៈ។"]
            ],
            [
                'title' => ['en' => 'Site Engineer', 'km' => 'វិស្វករការដ្ឋាន'],
                'slug' => 'site-engineer',
                'location' => ['en' => 'Siem Reap', 'km' => 'សៀមរាប'],
                'type' => 'FULL_TIME',
                'summary' => ['en' => 'Overseeing day-to-day operations on construction sites.', 'km' => 'ត្រួតពិនិត្យប្រតិបត្តិការប្រចាំថ្ងៃនៅការដ្ឋានសំណង់។'],
                'experience' => ['en' => '2-5 Years', 'km' => '២-៥ ឆ្នាំ'],
                'salary' => ['en' => 'Negotiable', 'km' => 'ចរចា'],
                'responsibilities' => ['en' => "Supervise site labor and subcontractors.\nTechnical review of architectural blueprints.\nReport progress to Project Managers.", 'km' => "ត្រួតពិនិត្យកម្លាំងពលកម្មនិងអ្នកម៉ៅការបន្ត។\nពិនិត្យបច្ចេកទេសលើប្លង់ស្ថាបត្យកម្ម។\nរាយការណ៍វឌ្ឍនភាពទៅអ្នកគ្រប់គ្រងគម្រោង។"],
                'requirements' => ['en' => "Degree in Civil Engineering or related field.\nPractical experience in site supervision.\nProblem-solving mindset.", 'km' => "សញ្ញាបត្រផ្នែកវិស្វកម្មស៊ីវិលឬជំនាញពាក់ព័ន្ធ។\nបទពិសោធន៍ជាក់ស្តែងក្នុងការត្រួតពិនិត្យការដ្ឋាន។\nផ្នត់គំនិតដោះស្រាយបញ្ហា។"],
                'benefits' => ['en' => "Accommodation allowance.\nSkills training.\nCareer growth track.", 'km' => "ប្រាក់ឧបត្ថម្ភការស្នាក់នៅ។\nការបណ្តុះបណ្តាលជំនាញ។\nផ្លូវកំណើនអាជីព។"]
            ]
        ];

        foreach ($jobs as $jobData) {
            JobPosting::updateOrCreate(
                ['slug' => $jobData['slug']],
                [
                    'title' => $jobData['title'],
                    'location' => $jobData['location'],
                    'type' => $jobData['type'],
                    'summary' => $jobData['summary'],
                    'experience' => $jobData['experience'],
                    'salary' => $jobData['salary'],
                    'responsibilities' => $jobData['responsibilities'],
                    'requirements' => $jobData['requirements'],
                    'benefits' => $jobData['benefits'],
                    'isActive' => true,
                ]
            );
        }
    }
}
