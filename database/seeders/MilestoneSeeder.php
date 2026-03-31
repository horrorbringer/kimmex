<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Milestone;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [
            [
                'year' => '1999',
                'title' => ['en' => 'Foundation', 'km' => 'ការបង្កើតឡើង'],
                'description' => ['en' => 'Kim Mex Construction & Investment Co.,Ltd. was established and registered in accordance with the regulations and laws of the Kingdom of Cambodia.', 'km' => 'ក្រុមហ៊ុន Kim Mex Construction & Investment Co.,Ltd. ត្រូវបានបង្កើតឡើង និងចុះបញ្ជីស្របតាមបទប្បញ្ញត្តិ និងច្បាប់នៃព្រះរាជាណាចក្រកម្ពុជា។'],
                'image' => 'images/projects/Thumbnail-1.jpg',
                'sortOrder' => 10,
            ],
            [
                'year' => '2001-2004',
                'title' => ['en' => 'Early Growth', 'km' => 'កំណើនដំបូង'],
                'description' => ['en' => 'Laying the groundwork for excellence in provincial infrastructure and building quality partnerships across the kingdom.', 'km' => 'ការរៀបចំមូលដ្ឋានគ្រឹះសម្រាប់ឧត្តមភាពនៅក្នុងហេដ្ឋារចនាសម្ព័ន្ធខេត្ត និងការកសាងភាពជាដៃគូប្រកបដោយគុណភាពនៅទូទាំងព្រះរាជាណាចក្រកម្ពុជា។'],
                'image' => 'images/projects/Thumbnail-2.jpg',
                'sortOrder' => 20,
            ],
            [
                'year' => '2005-2013',
                'title' => ['en' => 'Expanding Horizons', 'km' => 'ការពង្រីកវិសាលភាព'],
                'description' => ['en' => 'Significant expansion of services into specialized building construction and large-scale public utility projects.', 'km' => 'ការពង្រីកសេវាកម្មយ៉ាងសំខាន់ទៅក្នុងការសាងសង់អគារឯកទេស និងគម្រោងហេដ្ឋារចនាសម្ព័ន្ធសាធារណៈខ្នាតធំ។'],
                'image' => 'images/projects/Thumbnail-3.jpg',
                'sortOrder' => 30,
            ],
            [
                'year' => '2014-2017',
                'title' => ['en' => 'Institutional Partnerships', 'km' => 'ភាពជាដៃគូស្ថាប័ន'],
                'description' => ['en' => 'Delivery of key institutional projects including: Ministry of Economy and Finance, Ministry of Post and Telecommunication, Clean Water in Mondulkiri Province, Electricity of Cambodia Wat Phnom, Al Serkal Mosque.', 'km' => 'ការប្រគល់គម្រោងស្ថាប័នសំខាន់ៗរួមមាន៖ ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ, ក្រសួងប្រៃសណីយ៍ និងទូរគមនាគមន៍, ទឹកស្អាតខេត្តមណ្ឌលគិរី, អគ្គិសនីកម្ពុជា វត្តភ្នំ, វិហារអ៊ីស្លាម អាល់សឺកាល់។'],
                'image' => 'images/projects/Thumbnail-4.jpg',
                'sortOrder' => 40,
            ],
            [
                'year' => '2018-2020',
                'title' => ['en' => 'Scaling Innovation', 'km' => 'ការពង្រីកនវានុវត្តន៍'],
                'description' => ['en' => 'Integration of modern systems and complex structural works: Anti-Corruption Unit, Siem Reap Electricity, Ministry of Economy Underground Parking Lot, General Department of National Treasury.', 'km' => 'ការរួមបញ្ចូលនៃប្រព័ន្ធទំនើប និងការអនុវត្តរចនាសម្ព័ន្ធស្មុគស្មាញ៖ អង្គភាពប្រឆាំងអំពើពុករលួយ, អគ្គិសនីខេត្តសៀមរាប, ចំណតឡានក្រោមដីក្រសួងសេដ្ឋកិច្ច, អគ្គនាយកដ្ឋានរតនាគារជាតិ។'],
                'image' => 'images/projects/Thumbnail-5.jpg',
                'sortOrder' => 50,
            ],
            [
                'year' => '2021-2022',
                'title' => ['en' => 'Infrastructure Excellence', 'km' => 'ឧត្តមភាពហេដ្ឋារចនាសម្ព័ន្ធ'],
                'description' => ['en' => 'Securing major national landmarks and utility hubs: Stung Treng Water Purification Station, General Department of Customs and Excise, Securities and Exchange Commission of Cambodia, Electricity of Cambodia (EDC).', 'km' => 'សាងសង់ហេដ្ឋារចនាសម្ព័ន្ធសំខាន់ៗ និងមជ្ឈមណ្ឌលអគ្គិសនីជាតិ៖ ស្ថានីយ៍ប្រព្រឹត្តិកម្មទឹកស្អាតខេត្តស្ទឹងត្រែង, អគ្គនាយកដ្ឋានគយ និងរដ្ឋាករកម្ពុជា, គណៈកម្មការមូលបត្រកម្ពុជា, អគ្គិសនីកម្ពុជា (EDC)។'],
                'image' => 'images/projects/Thumbnail-6.jpg',
                'sortOrder' => 60,
            ],
            [
                'year' => '2023',
                'title' => ['en' => 'Strategic Progress', 'km' => 'វឌ្ឍនភាពយុទ្ធសាស្ត្រ'],
                'description' => ['en' => 'Completion of high-profile government headquarters: Ministry of Interior HQ, National Social Security Fund (NSSF).', 'km' => 'ការបញ្ចប់អគារទីស្នាក់ការកណ្តាលរបស់រដ្ឋាភិបាល៖ ទីស្នាក់ការកណ្តាល ក្រសួងមហាផ្ទៃ, បេឡាជាតិរបបសន្តិសុខសង្គម (ប.ស.ស)។'],
                'image' => 'images/projects/Thumbnail-7.jpg',
                'sortOrder' => 70,
            ],
            [
                'year' => '2024',
                'title' => ['en' => 'Future Foundations', 'km' => 'មូលដ្ឋានគ្រឹះអនាគត'],
                'description' => ['en' => 'Expanding into healthcare and regulatory sectors: Commercial Gambling Management Commission, Chea Chumneas Hospital.', 'km' => 'ការពង្រីកខ្លួនចូលក្នុងវិស័យថែទាំសុខភាព និងបទប្បញ្ញត្តិ៖ គណៈកម្មការគ្រប់គ្រងល្បែងពាណិជ្ជកម្មកម្ពុជា, មន្ទីរពេទ្យ ជ័យជំនះ។'],
                'image' => 'images/projects/Thumbnail-8.jpg',
                'sortOrder' => 80,
            ],
            [
                'year' => '2025',
                'title' => ['en' => 'Vision 2025', 'km' => 'ចក្ខុវិស័យ ២០២៥'],
                'description' => ['en' => 'Ongoing and future flagship developments: National Election Committee HQ.', 'km' => 'ការអភិវឌ្ឍកំពុងបន្ត និងគម្រោងសំខាន់ៗនាពេលអនាគត៖ ទីស្នាក់ការកណ្តាល គណៈកម្មាធិការជាតិរៀបចំការបោះឆ្នោត។'],
                'image' => 'images/projects/Thumbnail-9.jpg',
                'sortOrder' => 90,
            ],
        ];

        foreach ($milestones as $m) {
            Milestone::create($m);
        }
    }
}
