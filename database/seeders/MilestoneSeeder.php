<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Milestone;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        Milestone::truncate();

        $milestones = [
            [
                'year' => '1999',
                'title' => ['en' => 'Foundation', 'km' => 'ការបង្កើតឡើង'],
                'description' => ['en' => '<p>Kim Mex Construction & Investment Co.,Ltd. was established and registered in accordance with the regulations and laws of the Kingdom of Cambodia.</p>', 'km' => '<p>ក្រុមហ៊ុន Kim Mex Construction & Investment Co.,Ltd. ត្រូវបានបង្កើតឡើង និងចុះបញ្ជីស្របតាមបទប្បញ្ញត្តិ និងច្បាប់នៃព្រះរាជាណាចក្រកម្ពុជា។</p>'],
                'image' => 'images/projects/Thumbnail-1.jpg',
                'sortOrder' => 10,
            ],
            [
                'year' => '2001-2004',
                'title' => ['en' => 'Early Growth', 'km' => 'កំណើនដំបូង'],
                'description' => ['en' => '<p>Laying the groundwork for excellence in provincial infrastructure and building quality partnerships across the kingdom.</p>', 'km' => '<p>ការរៀបចំមូលដ្ឋានគ្រឹះសម្រាប់ឧត្តមភាពនៅក្នុងហេដ្ឋារចនាសម្ព័ន្ធខេត្ត និងការកសាងភាពជាដៃគូប្រកបដោយគុណភាពនៅទូទាំងព្រះរាជាណាចក្រកម្ពុជា។</p>'],
                'image' => 'images/projects/Thumbnail-2.jpg',
                'sortOrder' => 20,
            ],
            [
                'year' => '2005-2013',
                'title' => ['en' => 'Expanding Horizons', 'km' => 'ការពង្រីកវិសាលភាព'],
                'description' => ['en' => '<p>Significant expansion of services into specialized building construction and large-scale public utility projects.</p>', 'km' => '<p>ការពង្រីកសេវាកម្មយ៉ាងសំខាន់ទៅក្នុងការសាងសង់អគារឯកទេស និងគម្រោងហេដ្ឋារចនាសម្ព័ន្ធសាធារណៈខ្នាតធំ។</p>'],
                'image' => 'images/projects/Thumbnail-3.jpg',
                'sortOrder' => 30,
            ],
            [
                'year' => '2014-2017',
                'title' => ['en' => 'Institutional Partnerships', 'km' => 'ភាពជាដៃគូស្ថាប័ន'],
                'description' => [
                    'en' => '<p>Delivery of key institutional projects including:</p><ol><li>Ministry of Economy and Finance</li><li>Ministry of Post and Telecommunication</li><li>Clean Water in Mondulkiri Province</li><li>Electricity of Cambodia Wat Phnom</li><li>Al Serkal Mosque</li></ol>',
                    'km' => '<p>ការប្រគល់គម្រោងស្ថាប័នសំខាន់ៗរួមមាន៖</p><ol><li>ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ</li><li>ក្រសួងប្រៃសណីយ៍ និងទូរគមនាគមន៍</li><li>ទឹកស្អាតខេត្តមណ្ឌលគិរី</li><li>អគ្គិសនីកម្ពុជា វត្តភ្នំ</li><li>វិហារអ៊ីស្លាម អាល់សឺកាល់</li></ol>'
                ],
                'image' => 'images/projects/Thumbnail-4.jpg',
                'sortOrder' => 40,
            ],
            [
                'year' => '2018-2020',
                'title' => ['en' => 'Scaling Innovation', 'km' => 'ការពង្រីកនវានុវត្តន៍'],
                'description' => [
                    'en' => '<p>Integration of modern systems and complex structural works:</p><ol><li>Anti-Corruption Unit</li><li>Siem Reap Electricity</li><li>Ministry of Economy Underground Parking Lot</li><li>General Department of National Treasury</li></ol>',
                    'km' => '<p>ការរួមបញ្ចូលនៃប្រព័ន្ធទំនើប និងការអនុវត្តរចនាសម្ព័ន្ធស្មុគស្មាញ៖</p><ol><li>អង្គភាពប្រឆាំងអំពើពុករលួយ</li><li>អគ្គិសនីខេត្តសៀមរាប</li><li>ចំណតឡានក្រោមដីក្រសួងសេដ្ឋកិច្ច</li><li>អគ្គនាយកដ្ឋានរតនាគារជាតិ</li></ol>'
                ],
                'image' => 'images/projects/Thumbnail-5.jpg',
                'sortOrder' => 50,
            ],
            [
                'year' => '2021-2022',
                'title' => ['en' => 'Infrastructure Excellence', 'km' => 'ឧត្តមភាពហេដ្ឋារចនាសម្ព័ន្ធ'],
                'description' => [
                    'en' => '<p>Securing major national landmarks and utility hubs:</p><ol><li>Stung Treng Water Purification Station</li><li>General Department of Customs and Excise of Cambodia</li><li>Securities and Exchange Commission of Cambodia</li><li>Electricity of Cambodia</li></ol>',
                    'km' => '<p>សាងសង់ហេដ្ឋារចនាសម្ព័ន្ធសំខាន់ៗ និងមជ្ឈមណ្ឌលអគ្គិសនីជាតិ៖</p><ol><li>ស្ថានីយ៍ប្រព្រឹត្តិកម្មទឹកស្អាតខេត្តស្ទឹងត្រែង</li><li>អគ្គនាយកដ្ឋានគយ និងរដ្ឋាករកម្ពុជា</li><li>គណៈកម្មការមូលបត្រកម្ពុជា</li><li>អគ្គិសនីកម្ពុជា (EDC)</li></ol>'
                ],
                'image' => 'images/projects/Thumbnail-6.jpg',
                'sortOrder' => 60,
            ],
            [
                'year' => '2023',
                'title' => ['en' => 'Strategic Progress', 'km' => 'វឌ្ឍនភាពយុទ្ធសាស្ត្រ'],
                'description' => [
                    'en' => '<p>Completion of high-profile government headquarters:</p><ol><li>Ministry of Interior</li><li>National Social Security Fund</li></ol>',
                    'km' => '<p>ការបញ្ចប់អគារទីស្នាក់ការកណ្តាលរបស់រដ្ឋាភិបាល៖</p><ol><li>ក្រសួងមហាផ្ទៃ</li><li>បេឡាជាតិរបបសន្តិសុខសង្គម (ប.ស.ស)</li></ol>'
                ],
                'image' => 'images/projects/Thumbnail-7.jpg',
                'sortOrder' => 70,
            ],
            [
                'year' => '2024',
                'title' => ['en' => 'Future Foundations', 'km' => 'មូលដ្ឋានគ្រឹះអនាគត'],
                'description' => [
                    'en' => '<p>Expanding into healthcare and regulatory sectors:</p><ol><li>Commercial Gambling Management Commission of Cambodia</li><li>Chey Chumneas Hospital</li></ol>',
                    'km' => '<p>ការពង្រីកខ្លួនចូលក្នុងវិស័យថែទាំសុខភាព និងបទប្បញ្ញត្តិ៖</p><ol><li>គណៈកម្មការគ្រប់គ្រងល្បែងពាណិជ្ជកម្មកម្ពុជា</li><li>មន្ទីរពេទ្យ ជ័យជំនះ</li></ol>'
                ],
                'image' => 'images/projects/Thumbnail-8.jpg',
                'sortOrder' => 80,
            ],
            [
                'year' => '2025',
                'title' => ['en' => 'Vision 2025', 'km' => 'ចក្ខុវិស័យ ២០២៥'],
                'description' => [
                    'en' => '<p>Ongoing and future flagship developments:</p><ol><li>National Election Committee</li></ol>',
                    'km' => '<p>ការអភិវឌ្ឍកំពុងបន្ត និងគម្រោងសំខាន់ៗនាពេលអនាគត៖</p><ol><li>គណៈកម្មាធិការជាតិរៀបចំការបោះឆ្នោត</li></ol>'
                ],
                'image' => 'images/projects/Thumbnail-9.jpg',
                'sortOrder' => 90,
            ],
        ];

        foreach ($milestones as $m) {
            Milestone::create($m);
        }
    }
}
