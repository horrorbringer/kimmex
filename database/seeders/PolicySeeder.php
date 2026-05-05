<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'title' => [
                    'en' => 'Quality Management Policy',
                    'km' => 'គោលការណ៍គ្រប់គ្រងគុណភាព',
                ],
                'slug' => 'quality-policy',
                'icon' => 'heroicon-o-check-badge',
                'sort_order' => 1,
                'content' => [
                    'en' => '<h3>Our Commitment to Quality</h3><p>KIMMEX is committed to delivering excellence in every construction and investment project. Our quality management system adheres to ISO 9001:2015 standards, ensuring that we meet and exceed client expectations while maintaining regulatory compliance.</p><ul><li>Continuous improvement of our processes</li><li>Rigorous QA/QC monitoring at all project stages</li><li>Empowering employees through training and development</li></ul>',
                    'km' => '<h3>ការប្តេជ្ញាចិត្តចំពោះគុណភាព</h3><p>KIMMEX ប្តេជ្ញាផ្តល់នូវឧត្តមភាពក្នុងគ្រប់គម្រោងសាងសង់ និងវិនិយោគ។ ប្រព័ន្ធគ្រប់គ្រងគុណភាពរបស់យើងអនុលោមតាមស្តង់ដារ ISO 9001:2015 ដោយធានាថាយើងបំពេញ និងលើសពីការរំពឹងទុករបស់អតិថិជន ខណៈពេលដែលរក្សាបាននូវការអនុលោមតាមបទប្បញ្ញត្តិ។</p><ul><li>ការកែលម្អជាបន្តបន្ទាប់នៃដំណើរការរបស់យើង</li><li>ការត្រួតពិនិត្យ QA/QC យ៉ាងម៉ឺងម៉ាត់នៅគ្រប់ដំណាក់កាលនៃគម្រោង</li><li>ការផ្តល់សិទ្ធិអំណាចដល់បុគ្គលិកតាមរយៈការបណ្តុះបណ្តាល និងការអភិវឌ្ឍន៍</li></ul>',
                ],
            ],
            [
                'title' => [
                    'en' => 'Occupational Health & Safety Policy',
                    'km' => 'គោលការណ៍សុខភាព និងសុវត្ថិភាពការងារ',
                ],
                'slug' => 'health-safety-policy',
                'icon' => 'heroicon-o-shield-check',
                'sort_order' => 2,
                'content' => [
                    'en' => '<h3>Safety First, Always</h3><p>At KIMMEX, the safety of our employees, contractors, and the public is our top priority. We maintain a "Zero Harm" objective across all our construction sites and offices.</p><ul><li>Mandatory PPE compliance for all site personnel</li><li>Regular safety audits and risk assessments</li><li>Emergency response preparedness training</li></ul>',
                    'km' => '<h3>សុវត្ថិភាពជាចម្បង ជានិច្ចកាល</h3><p>នៅ KIMMEX សុវត្ថិភាពរបស់បុគ្គលិក អ្នកម៉ៅការ និងសាធារណៈជន គឺជាអាទិភាពចម្បងរបស់យើង។ យើងរក្សានូវគោលដៅ "គ្រោះថ្នាក់សូន្យ" នៅគ្រប់ការដ្ឋានសំណង់ និងការិយាល័យរបស់យើង។</p><ul><li>ការអនុលោមតាម PPE ជាកាតព្វកិច្ចសម្រាប់បុគ្គលិកការដ្ឋានទាំងអស់</li><li>ការធ្វើសវនកម្មសុវត្ថិភាព និងការវាយតម្លៃហានិភ័យជាទៀងទាត់</li><li>ការបណ្តុះបណ្តាលការត្រៀមលក្ខណៈឆ្លើយតបនឹងគ្រោះអាសន្ន</li></ul>',
                ],
            ],
            [
                'title' => [
                    'en' => 'Environmental Protection Policy',
                    'km' => 'គោលការណ៍ការពារបរិស្ថាន',
                ],
                'slug' => 'environmental-policy',
                'icon' => 'heroicon-o-globe-alt',
                'sort_order' => 3,
                'content' => [
                    'en' => '<h3>Sustainable Construction Practices</h3><p>KIMMEX recognizes its responsibility to protect the environment. We strive to minimize our ecological footprint by implementing sustainable construction practices and efficient resource management.</p><ul><li>Waste reduction and recycling programs</li><li>Efficient energy and water consumption</li><li>Compliance with local environmental regulations</li></ul>',
                    'km' => '<h3>ការអនុវត្តសំណង់ប្រកបដោយនិរន្តរភាព</h3><p>KIMMEX ទទួលស្គាល់ការទទួលខុសត្រូវរបស់ខ្លួនក្នុងការការពារបរិស្ថាន។ យើងខិតខំកាត់បន្ថយផលប៉ះពាល់អេកូឡូស៊ីរបស់យើង ដោយអនុវត្តការអនុវត្តសំណង់ប្រកបដោយនិរន្តរភាព និងការគ្រប់គ្រងធនធានប្រកបដោយប្រសិទ្ធភាព។</p><ul><li>កម្មវិធីកាត់បន្ថយសំណល់ និងការកែច្នៃឡើងវិញ</li><li>ការប្រើប្រាស់ថាមពល និងទឹកប្រកបដោយប្រសិទ្ធភាព</li><li>ការអនុលោមតាមបទប្បញ្ញត្តិបរិស្ថានក្នុងតំបន់</li></ul>',
                ],
            ],
            [
                'title' => [
                    'en' => 'Corporate Ethics & Anti-Corruption Policy',
                    'km' => 'គោលការណ៍ក្រមសីលធម៌សាជីវកម្ម និងប្រឆាំងអំពើពុករលួយ',
                ],
                'slug' => 'ethics-policy',
                'icon' => 'heroicon-o-finger-print',
                'sort_order' => 4,
                'content' => [
                    'en' => '<h3>Integrity and Transparency</h3><p>KIMMEX operates with the highest level of integrity and transparency. We have a zero-tolerance policy towards bribery, corruption, and any form of unethical business behavior.</p><ul><li>Strict adherence to the Anti-Corruption Law of Cambodia</li><li>Whistleblower protection and confidential reporting</li><li>Regular compliance training for all management and staff</li></ul>',
                    'km' => '<h3>សុចរិតភាព និងតម្លាភាព</h3><p>KIMMEX ប្រតិបត្តិការជាមួយកម្រិតខ្ពស់បំផុតនៃសុចរិតភាព និងតម្លាភាព។ យើងមានគោលការណ៍មិនអត់ឱនចំពោះអំពើពុករលួយ អំពើពុករលួយ និងទម្រង់ណាមួយនៃអាកប្បកិរិយាអាជីវកម្មដែលមិនមានសីលធម៌។</p><ul><li>ការអនុលោមយ៉ាងតឹងរ៉ឹងតាមច្បាប់ប្រឆាំងអំពើពុករលួយនៃព្រះរាជាណាចក្រកម្ពុជា</li><li>ការការពារអ្នកផ្តល់ព័ត៌មាន និងការរាយការណ៍សម្ងាត់</li><li>ការបណ្តុះបណ្តាលអនុលោមភាពជាទៀងទាត់សម្រាប់ថ្នាក់ដឹកនាំ និងបុគ្គលិកទាំងអស់</li></ul>',
                ],
            ],
        ];

        foreach ($policies as $data) {
            Policy::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'icon' => $data['icon'],
                    'sort_order' => $data['sort_order'],
                    'is_public' => true,
                ]
            );
        }
    }
}
