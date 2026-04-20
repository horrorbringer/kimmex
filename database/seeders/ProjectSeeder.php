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
        $cat = ProjectCategory::all()->pluck('id', 'slug')->toArray();

        $img = [
            'gov' => 'projects/realproject/government-building.png',
            'water' => 'projects/realproject/water-treatment.png',
            'health' => 'projects/realproject/hospital.png',
            'school' => 'projects/realproject/school.png',
            'factory' => 'projects/realproject/factory.png',
            'energy' => 'projects/realproject/energy-utility.jpg',
            'mosque' => 'projects/realproject/mosque.jpg',
        ];

        $projects = [
            // 1. NATIONAL ELECTION COMMITTEE (NEC)
            [
                'title' => ['en' => 'National Election Committee (NEC)', 'km' => 'គណៈកម្មាធិការជាតិរៀបចំការបោះឆ្នោត (គ.ជ.ប)'],
                'slug' => 'national-election-committee',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'NEC',
                'scale' => '9 Floor | 32,000 m²',
                'timeline' => 'Jan 2023 - Dec 2025',
                'completionDate' => '2025-12-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => true,
                'status' => ProjectStatus::ONGOING,
                'description' => [
                    'en' => '9-floor classical Khmer-style headquarters for the NEC.',
                    'km' => 'អគារស្នាក់ការកណ្តាល ៩ ជាន់ បែបស្ថាបត្យកម្មខ្មែរបុរាណ សម្រាប់ គ.ជ.ប។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Concrete reinforced concrete, 40m deep foundation</li><li>Classic building structure</li><li>Generator, telephone, Ethernet, audio, air conditioning system</li><li>Fire extinguishing system, BMS-9 elevators, 1 service elevator</li></ul>',
                    'km' => '<ul><li>បេតុងអាមេ គ្រឹះជម្រៅ ៤០ម៉ែត្រ</li><li>រចនាសម្ព័ន្ធអគារបែបបុរាណ</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង និងប្រព័ន្ធម៉ាស៊ីនត្រជាក់</li><li>ប្រព័ន្ធពន្លត់អគ្គិភ័យ ជណ្ដើរយន្ត BMS-9 និងជណ្ដើរយន្តសេវាកម្ម ១</li></ul>'
                ],
            ],
            // 2. COMMERCIAL GAMBLING MANAGEMENT COMMISSION OF CAMBODIA
            [
                'title' => ['en' => 'Commercial Gambling Management Commission of Cambodia', 'km' => 'គណៈកម្មការគ្រប់គ្រងល្បែងពាណិជ្ជកម្មកម្ពុជា'],
                'slug' => 'commercial-gambling-management-commission',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'CGMC',
                'scale' => '18 Floor | 6,955 m²',
                'timeline' => 'Jan 2023 - Dec 2024',
                'completionDate' => '2024-12-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::ONGOING,
                'description' => [
                    'en' => '18-floor modern building with glass walls and terrace roof.',
                    'km' => 'អគារទំនើប ១៨ ជាន់ ជាមួយនឹងជញ្ជាំងកញ្ចក់ និងដំបូលរាបស្មើ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Teleportation and fence work</li><li>Concrete reinforced concrete, 40m deep foundation</li><li>Modern building structure with glass walls and terrace roof</li><li>Generator, telephone, Ethernet, audio, air conditioning system work</li><li>Fire extinguishing system, BMS-3 elevators, 1 car elevator</li></ul>',
                    'km' => '<ul><li>ការងារទូរស័ព្ទ និងរបង</li><li>បេតុងអាមេ គ្រឹះជម្រៅ ៤០ម៉ែត្រ</li><li>រចនាសម្ព័ន្ធអគារទំនើបជញ្ជាំងកញ្ចក់ និងដំបូលរាបស្មើ</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង និងប្រព័ន្ធម៉ាស៊ីនត្រជាក់</li><li>ប្រព័ន្ធពន្លត់អគ្គិភ័យ ជណ្ដើរយន្ត BMS-3 និងជណ្ដើរយន្តឡាន ១</li></ul>'
                ],
            ],
            // 3. CHEA CHUMNEA HOSPITAL
            [
                'title' => ['en' => 'Chea Chumnea Hospital', 'km' => 'មន្ទីរពេទ្យជ័យជំនះ'],
                'slug' => 'chey-chumneas-hospital',
                'location' => ['en' => 'Kandal, Cambodia', 'km' => 'កណ្តាល, កម្ពុជា'],
                'client' => 'Ministry of Health',
                'scale' => '7 Floor | 7,098 m²',
                'timeline' => 'Feb 2023 - Aug 2024',
                'completionDate' => '2024-08-01',
                'heroImage' => $img['health'],
                'project_category_id' => $cat['healthcare'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::ONGOING,
                'description' => [
                    'en' => '7-floor hospital building with specialized medical infrastructure.',
                    'km' => 'អគារមន្ទីរពេទ្យ ៧ ជាន់ ជាមួយនឹងហេដ្ឋារចនាសម្ព័ន្ធវេជ្ជសាស្ត្រឯកទេស។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Bridge and fence work</li><li>Concrete reinforcement, 37m deep foundation</li><li>Water and electricity system work, generator, air conditioner</li><li>Oxygen system, 2 elevators</li></ul>',
                    'km' => '<ul><li>ការងារស្ពាន និងរបង</li><li>ការពង្រឹងបេតុង គ្រឹះជម្រៅ ៣៧ម៉ែត្រ</li><li>ប្រព័ន្ធទឹក ភ្លើង ម៉ាស៊ីនភ្លើង និងម៉ាស៊ីនត្រជាក់</li><li>ប្រព័ន្ធអុកស៊ីហ្សែន និងជណ្ដើរយន្ត ២</li></ul>'
                ],
            ],
            // 4. NATIONAL SOCIAL SECURITY FUND (NSSF)
            [
                'title' => ['en' => 'National Social Security Fund (NSSF)', 'km' => 'មូលនិធិជាតិសន្តិសុខសង្គម (ប.ស.ស)'],
                'slug' => 'nssf-headquarters',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'NSSF',
                'scale' => '20 Floor | 56,808 m²',
                'timeline' => 'Jan 2023 - Dec 2024',
                'completionDate' => '2024-12-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => true,
                'status' => ProjectStatus::ONGOING,
                'description' => [
                    'en' => '20-floor state-of-the-art building designed for efficient social security services.',
                    'km' => 'អគារទំនើប ២០ ជាន់ ដែលរចនាឡើងដើម្បីផ្តល់សេវាសន្តិសុខសង្គមប្រកបដោយប្រសិទ្ធភាព។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Roofing and fencing work</li><li>Concrete reinforced concrete, 40m deep foundation</li><li>Classic building structure and terrace roof for Sky Bar</li><li>Generator, Telephone, Network system, audio, air conditioning system</li><li>Fire extinguishing system and BMS system</li><li>8 elevators, parking lot at the back of the building</li></ul>',
                    'km' => '<ul><li>ការងារដំបូល และរបង</li><li>បេតុងអាមេ គ្រឹះជម្រៅ ៤០ម៉ែត្រ</li><li>រចនាសម្ព័ន្ធអគារបុរាណ និង Sky Bar</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង និងប្រព័ន្ធម៉ាស៊ីនត្រជាក់</li><li>ប្រព័ន្ធពន្លត់អគ្គិភ័យ និងប្រព័ន្ធ BMS</li><li>ជណ្ដើរយន្ត ៨ និងចំណតរថយន្តនៅខាងក្រោយអគារ</li></ul>'
                ],
            ],
            // 5. MINISTRY OF INTERIOR
            [
                'title' => ['en' => 'Ministry of Interior', 'km' => 'ក្រសួងមហាផ្ទៃ'],
                'slug' => 'ministry-of-interior',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'Ministry of Interior',
                'scale' => '9 Floor | 105,545 m²',
                'timeline' => 'Nov 2021 - Jul 2023',
                'completionDate' => '2023-07-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => true,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '9-floor large-scale administrative building with classic Khmer architecture.',
                    'km' => 'អគាររដ្ឋបាលខ្នាតធំ ៩ ជាន់ ជាមួយនឹងស្ថាបត្យកម្មខ្មែរបុរាណ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Bridge and fence work</li><li>Concrete type of reinforcement, foundation type of drilling, 40m deep</li><li>Classic building structure and tiled roof with 5 busbars</li><li>Generator, Telephone, Network system, audio, air conditioning system, Access control, fire alarm, PA system, CCTV system, security scan equipment and data center facility</li><li>Fire extinguishing system and BMS system</li><li>11 elevators, 20 escalators</li></ul>',
                    'km' => '<ul><li>ការងារស្ពាន និងរបង</li><li>ការពង្រឹងបេតុង គ្រឹះស្ពានជម្រៅ ៤០ម៉ែត្រ</li><li>រចនាសម្ព័ន្ធអគារបែបបុរាណ និងដំបូលក្បឿងជាមួយ ៥ busbars</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង ម៉ាស៊ីនត្រជាក់ ប្រព័ន្ធគ្រប់គ្រងការចូល អាឡាមអគ្គិភ័យ ប្រព័ន្ធ PA ប្រព័ន្ធ CCTV ឧបករណ៍ស្កែនសន្តិសុខ និងមជ្ឈមណ្ឌលផ្ទុកទិន្នន័យ (Data Center)</li><li>ប្រព័ន្ធពន្លត់អគ្គិភ័យ និងប្រព័ន្ធ BMS</li><li>ជណ្ដើរយន្ត ១១ និងជណ្ដើរយន្តប្រអប់ ២០</li></ul>'
                ],
            ],
            // 6. STUNG TRENG PROVINCE WATER PURIFICATION STATION
            [
                'title' => ['en' => 'Stung Treng Province Water Purification Station', 'km' => 'ស្ថានីយ៍ចម្រោះទឹកស្អាតខេត្តស្ទឹងត្រែង'],
                'slug' => 'stung-treng-water-station',
                'location' => ['en' => 'Stung Treng, Cambodia', 'km' => 'ស្ទឹងត្រែង, កម្ពុជា'],
                'client' => 'Provincial Water Authority',
                'scale' => '10,000 m²',
                'timeline' => 'Oct 2019 - Oct 2021',
                'completionDate' => '2021-10-01',
                'heroImage' => $img['water'],
                'project_category_id' => $cat['infrastructure'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Large-scale water purification and distribution facility serving Stung Treng province.',
                    'km' => 'រោងចក្របូមទឹក និងចម្រោះទឹកស្អាតខ្នាតធំសម្រាប់បម្រើប្រជាជនក្នុងខេត្តស្ទឹងត្រែង។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Work to the pipeline and fence</li><li>Reaction tank size 25000 square meters</li><li>Raw water pumping station to the filtration site</li><li>Water distribution network to the provincial pipeline</li></ul>',
                    'km' => '<ul><li>ការងារបណ្តាញទុយោ និងរបង</li><li>អាងចម្រោះទំហំ ២៥,០០០ ម៉ែត្រការ៉េ</li><li>ស្ថានីយ៍បូមទឹកឆៅទៅមជ្ឈមណ្ឌលចម្រោះ</li><li>បណ្តាញចែកចាយទឹកស្អាតទៅកាន់បំពង់មេខេត្ត</li></ul>'
                ],
            ],
            // 7. GENERAL DEPARTMENT OF CUSTOMS AND EXCISE OF CAMBODIA
            [
                'title' => ['en' => 'General Department of Customs and Excise of Cambodia', 'km' => 'អគ្គនាយកដ្ឋានគយ និងរដ្ឋាករកម្ពុជា'],
                'slug' => 'gdce-building',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'GDCE',
                'scale' => '35 Floor | 46,000 m²',
                'timeline' => 'Oct 2017 - Jul 2021',
                'completionDate' => '2021-07-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => true,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '35-floor high-rise administration headquarters for the Customs and Excise department.',
                    'km' => 'អគាររដ្ឋបាលខ្ពស់ស្កឹមស្កៃ ៣៥ ជាន់ សម្រាប់អគ្គនាយកដ្ឋានគយ និងរដ្ឋាករ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Bridge and fence work</li><li>Concrete type of reinforcement, foundation type of drilling, 40m deep</li><li>Classic building structure and tiled roof with 5 busbars</li><li>Generator, Telephone, Network system, audio, air conditioning system, Access control, fire alarm, PA system, CCTV system, security scan equipment and data center facility</li><li>Fire extinguishing system and BMS system</li><li>12 elevators, parking to the 5th floor</li></ul>',
                    'km' => '<ul><li>ការងារស្ពាន និងរបង</li><li>ការពង្រឹងបេតុង គ្រឹះស្ពានជម្រៅ ៤០ម៉ែត្រ</li><li>រចនាសម្ព័ន្ធអគារបែបបុរាណ និងដំបូលក្បឿងជាមួយ ៥ busbars</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង ម៉ាស៊ីនត្រជាក់ ប្រព័ន្ធគ្រប់គ្រងការចូល អាឡាមអគ្គិភ័យ ប្រព័ន្ធ PA ប្រព័ន្ធ CCTV ឧបករណ៍ស្កែនសន្តិសុខ និងមជ្ឈមណ្ឌលផ្ទុកទិន្នន័យ (Data Center)</li><li>ប្រព័ន្ធពន្លត់អគ្គិភ័យ និងប្រព័ន្ធ BMS</li><li>ជណ្ដើរយន្ត ១២ និងចំណតរថយន្តរហូតដល់ជាន់ទី ៥</li></ul>'
                ],
            ],
            // 8. SECURITY AND EXCHANGE COMMISSION OF CAMBODIA
            [
                'title' => ['en' => 'Security and Exchange Commission of Cambodia', 'km' => 'និយតករមូលបត្រកម្ពុជា'],
                'slug' => 'serc-building',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'SERC',
                'scale' => '14 Floor | 11,917 m²',
                'timeline' => 'Jun 2018 - Jul 2021',
                'completionDate' => '2021-07-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '14-floor high-rise administration building for the SEC.',
                    'km' => 'អគាររដ្ឋបាលខ្ពស់ស្កឹមស្កៃ ១៤ ជាន់ សម្រាបនិយតករមូលបត្រ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Bridge and fence work</li><li>Concrete reinforced concrete, 40m deep foundation</li><li>Classic building structure and terrace roof</li><li>Generator, Telephone, Network system, audio, air conditioning system, Access control, fire alarm, PA system, CCTV system, Parking system</li><li>Fire extinguishing system</li><li>4 elevators</li></ul>',
                    'km' => '<ul><li>ការងារស្ពាន និងរបង</li><li>បេតុងអាមេ គ្រឹះជម្រៅ ៤០ម៉ែត្រ</li><li>រចនាសម្ព័ន្ធអគារបែបបុរាណ និងដំបូលរាបស្មើ</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង ម៉ាស៊ីនត្រជាក់ ប្រព័ន្ធគ្រប់គ្រងការចូល អាឡាមអគ្គិភ័យ ប្រព័ន្ធ PA ប្រព័ន្ធ CCTV និងប្រព័ន្ធចំណត</li><li>ប្រព័ន្ធពន្លត់អគ្គិភ័យ</li><li>ជណ្ដើរយន្ត ៤</li></ul>'
                ],
            ],
            // 9. WAT PHNOM ELECTRICITY CONSTRUCTION AND EXPANSION
            [
                'title' => ['en' => 'Wat Phnom Electricity Construction and Expansion', 'km' => 'ការពង្រីក និងសាងសង់អគ្គិសនីវត្តភ្នំ'],
                'slug' => 'wat-phnom-electricity-expansion',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'EDC',
                'scale' => '8 Floor | 17,522 m²',
                'timeline' => 'Jan 2019 - Jun 2021',
                'completionDate' => '2021-06-01',
                'heroImage' => $img['energy'],
                'project_category_id' => $cat['energy'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '8-floor facility expansion for EDC including a modern data center.',
                    'km' => 'ការពង្រីកមជ្ឈមណ្ឌល ៨ ជាន់ សម្រាប់ EDC រួមទាំងមជ្ឈមណ្ឌលទិន្នន័យទំនើប។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Telecommunication and fence work</li><li>Front size 100cm x 90cm x 31m depth</li><li>Generator system work, telephone, Ethernet, voice</li><li>Air conditioning system, fire protection system, Data Center</li><li>4 elevators</li></ul>',
                    'km' => '<ul><li>ការងារទូរគមនាគមន៍ និងរបង</li><li>ទំហំខាងមុខ ១០០សង់ទីម៉ែត្រ x ៩០សង់ទីម៉ែត្រ x ៣១ម៉ែត្រ</li><li>ការងារប្រព័ន្ធម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ ប្រព័ន្ធការពារអគ្គិភ័យ និងមជ្ឈមណ្ឌលទិន្នន័យ Data Center</li><li>ជណ្ដើរយន្ត ៤</li></ul>'
                ],
            ],
            // 10. MINISTRY OF ECONOMY (UNDERGROUND PARKING LOT)
            [
                'title' => ['en' => 'Ministry of Economy (Underground Parking Lot)', 'km' => 'ក្រសួងសេដ្ឋកិច្ច (ចំណតក្រោមដី)'],
                'slug' => 'mef-underground-parking',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'MEF',
                'scale' => '2 Floor | 11,400 m²',
                'timeline' => 'Nov 2018 - Nov 2020',
                'completionDate' => '2020-11-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '2-floor underground parking structure to serve the Ministry of Economy.',
                    'km' => 'ចំណតរថយន្តក្រោមដី ២ ជាន់ សម្រាប់បម្រើការជូនក្រសួងសេដ្ឋកិច្ច។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Earth retaining wall and concrete slab work</li><li>2 underground parking floors</li><li>Generator, fire and ventilation system work</li><li>Underground road to the ministry</li><li>Air conditioning system, 1 elevator</li></ul>',
                    'km' => '<ul><li>ការងារជញ្ជាំងទប់ដី និងកម្រាលបេតុង</li><li>ចំណតក្រោមដី ២ ជាន់</li><li>ម៉ាស៊ីនភ្លើង ប្រព័ន្ធពន្លត់អគ្គិភ័យ និងប្រព័ន្ធខ្យល់ចេញចូល</li><li>ផ្លូវក្រោមដីតភ្ជាប់ទៅកាន់ក្រសួង</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ និងជណ្ដើរយន្ត ១</li></ul>'
                ],
            ],
            // 11. ANTI-CORRUPTION UNIT
            [
                'title' => ['en' => 'Anti-Corruption Unit', 'km' => 'អង្គភាពប្រឆាំងអំពើពុករលួយ'],
                'slug' => 'anti-corruption-unit',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'ACU',
                'scale' => '12 Floor | 8,950 m²',
                'timeline' => 'Oct 2019 - Oct 2020',
                'completionDate' => '2020-10-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '12-floor office building with premium finishes for ACU.',
                    'km' => 'អគារការិយាល័យ ១២ ជាន់ ជាមួយនឹងការតុបតែងកម្រិតខ្ពស់ សម្រាប់ ACU។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Bridge and fence work</li><li>Concrete reinforced concrete, 40m deep bored foundation</li><li>Classic building structure and terrace roof</li><li>Generator, telephone, Ethernet, audio, air conditioning system work</li><li>Fire extinguishing system, BMS</li><li>3 elevators, 1 car elevator</li></ul>',
                    'km' => '<ul><li>ការងារស្ពាន និងរបង</li><li>បេតុងអាមេ គ្រឹះខួងជម្រៅ ៤០ម៉ែត្រ</li><li>រចនាសម្ព័ន្ធអគារបែបបុរាណ និងដំបូលរាបស្មើ</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង និងប្រព័ន្ធម៉ាស៊ីនត្រជាក់</li><li>ប្រព័ន្ធពន្លត់អគ្គិភ័យ និង BMS</li><li>ជណ្ដើរយន្ត ៣ និងជណ្ដើរយន្តឡាន ១</li></ul>'
                ],
            ],
            // 12. SIEM REAP ELECTRICITY
            [
                'title' => ['en' => 'Siem Reap Electricity', 'km' => 'អគ្គិសនីសៀមរាប'],
                'slug' => 'siem-reap-electricity',
                'location' => ['en' => 'Siem Reap, Cambodia', 'km' => 'សៀមរាប, កម្ពុជា'],
                'client' => 'EDC',
                'scale' => '6 Floor | 4,970 m²',
                'timeline' => 'Jan 2019 - Mar 2020',
                'completionDate' => '2020-03-01',
                'heroImage' => $img['energy'],
                'project_category_id' => $cat['energy'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '6-floor administrative building supporting electricity distribution in Siem Reap.',
                    'km' => 'អគាររដ្ឋបាល ៦ ជាន់ សម្រាប់គាំទ្រការចែកចាយអគ្គិសនីក្នុងខេត្តសៀមរាប។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Roofing and fencing work</li><li>120cm x 100cm x 12m depth</li><li>Generator, telephone, internet, audio system, Access control and WiFi</li><li>Air conditioning system, 1 elevator</li></ul>',
                    'km' => '<ul><li>ការងារដំបូល និងរបង</li><li>ទំហំគ្រឹះ ១២០សង់ទីម៉ែត្រ x ១០០សង់ទីម៉ែត្រ x ១២ម៉ែត្រ</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង ប្រព័ន្ធគ្រប់គ្រងការចូល និង WiFi</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ និងជណ្ដើរយន្ត ១</li></ul>'
                ],
            ],
            // 13. GENERAL DEPARTMENT OF NATIONAL TREASURY
            [
                'title' => ['en' => 'General Department of National Treasury', 'km' => 'អគ្គនាយកដ្ឋានរតនាគារជាតិ'],
                'slug' => 'gdnt-building',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'GDNT',
                'scale' => '4 Floor | 2,945 m²',
                'timeline' => 'Nov 2017 - Nov 2019',
                'completionDate' => '2019-11-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '4-floor government administration building for the National Treasury.',
                    'km' => 'អគាររដ្ឋបាលរដ្ឋាភិបាល ៤ ជាន់ សម្រាប់រតនាគារជាតិ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Fence and railing work</li><li>120cm x 100cm x 12m depth foundation</li><li>Generator, telephone, Ethernet, voice system WiFi system</li><li>Building bridge</li><li>Air conditioning system, 1 elevator</li></ul>',
                    'km' => '<ul><li>ការងាររបង និងដៃជណ្តើរ</li><li>គ្រឹះជម្រៅ ១២ម៉ែត្រ</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត សំឡេង និង WiFi</li><li>ស្ពានតភ្ជាប់អគារ</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ និងជណ្ដើរយន្ត ១</li></ul>'
                ],
            ],
            // 14. MINISTRY OF ECONOMY AND FINANCE (BUILDING C)
            [
                'title' => ['en' => 'Ministry of Economy and Finance (Building C)', 'km' => 'ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ (អគារ C)'],
                'slug' => 'mef-building-c',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'MEF',
                'scale' => '9 Floor | 9,505 m²',
                'timeline' => 'Nov 2015 - Nov 2017',
                'completionDate' => '2017-11-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '9-floor office expansion for the Ministry of Economy and Finance.',
                    'km' => 'ការពង្រីកអគារការិយាល័យ ៩ ជាន់ សម្រាប់ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Roofing and fencing work</li><li>Front dimensions 120cm x 100cm x 12m depth</li><li>Building C</li><li>Generator, telephone, Ethernet, voice system work</li><li>Bridge over building 2</li><li>Air conditioning system, 2 elevators</li></ul>',
                    'km' => '<ul><li>ការងារដំបូល និងរបង</li><li>ទំហំខាងមុខ ១២០សង់ទីម៉ែត្រ x ១០០សង់ទីម៉ែត្រ x ១២ម៉ែត្រ</li><li>អគារ C</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត និងសំឡេង</li><li>ស្ពានតភ្ជាប់ពីលើអគារ ២</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ និងជណ្ដើរយន្ត ២</li></ul>'
                ],
            ],
            // 15. SOCFIN RUBBER PROCESSING FACTORY
            [
                'title' => ['en' => 'SOCFIN Rubber Processing Factory', 'km' => 'រោងចក្រកែច្នៃកៅស៊ូ SOCFIN'],
                'slug' => 'socfin-factory',
                'location' => ['en' => 'Mondulkiri, Cambodia', 'km' => 'មណ្ឌលគិរី, កម្ពុជា'],
                'client' => 'SOCFIN',
                'scale' => 'Industrial Renovation',
                'timeline' => 'Mar 2017 - Mar 2018',
                'completionDate' => '2018-03-01',
                'heroImage' => $img['factory'],
                'project_category_id' => $cat['commercial'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Renovation and extension of the rubber processing factory infrastructure.',
                    'km' => 'ការជួសជុល និងពង្រីកហេដ្ឋារចនាសម្ព័ន្ធរោងចក្រកែច្នៃកៅស៊ូ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Office Lab (Renovation + Extension)</li><li>Work Shop (Renovation)</li><li>Cafeteria & Meeting room (Renovation)</li><li>Changing Room, toilets & Showers (Renovation)</li><li>Weigh bridge office (Renovation)</li><li>Weigh bridge equipment – Delivery, installation and calibration</li></ul>',
                    'km' => '<ul><li>មន្ទីរពិសោធន៍ការិយាល័យ (ជួសជុល និងពង្រីក)</li><li>រោងជាង (ជួសជុល)</li><li>ហាងបាយ និងបន្ទប់ប្រជុំ (ជួសជុល)</li><li>បន្ទប់ផ្លាស់សម្លៀកបំពាក់ បង្គន់ និងកន្លែងងូតទឹក (ជួសជុល)</li><li>ការិយាល័យជញ្ជីងថ្លឹង (ជួសជុល)</li><li>ឧបករណ៍ជញ្ជីងថ្លឹង - ការដឹកជញ្ជូន ការដំឡើង និងការក្រិតតាមខ្នាត</li></ul>'
                ],
            ],
            // 16. MINISTRY OF POST AND TELECOMMUNICATION
            [
                'title' => ['en' => 'Ministry of Post and Telecommunication', 'km' => 'ក្រសួងប្រៃសណីយ៍ និងទូរគមនាគមន៍'],
                'slug' => 'mpt-office',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'MPTC',
                'scale' => '4 Floor | 8,950 m²',
                'timeline' => 'Oct 2014 - Oct 2016',
                'completionDate' => '2016-10-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '4-floor government building complex incorporating multiple office wings and warehouses.',
                    'km' => 'អគាររដ្ឋាភិបាល ៤ ជាន់ ដែលរួមមានអគារការិយាល័យ និងឃ្លាំងជាច្រើន។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Fence and railing work</li><li>Front dimensions 120cm 100cm 80cm Depth 12m</li><li>Building A, Building B, Building C, Warehouse A, Warehouse B</li><li>Generator system work, telephone, Ethernet, voice</li><li>Air conditioning system, 2 elevators</li></ul>',
                    'km' => '<ul><li>ការងាររបង និងដៃជណ្តើរ</li><li>គ្រឹះជម្រៅ ១២ម៉ែត្រ</li><li>អគារ A, B, C និងឃ្លាំង A, B</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត និងសំឡេង</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ និងជណ្ដើរយន្ត ២</li></ul>'
                ],
            ],
            // 17. CLEAN WATER IN MONDUKIRI PROVINCE
            [
                'title' => ['en' => 'Clean Water in Mondulkiri Province', 'km' => 'ទឹកស្អាតក្នុងខេត្តមណ្ឌលគិរី'],
                'slug' => 'mondulkiri-water-station',
                'location' => ['en' => 'Mondulkiri, Cambodia', 'km' => 'មណ្ឌលគិរី, កម្ពុជា'],
                'client' => 'MME',
                'scale' => '1,800 m²',
                'timeline' => 'Jan 2015 - Feb 2016',
                'completionDate' => '2016-02-01',
                'heroImage' => $img['water'],
                'project_category_id' => $cat['infrastructure'] ?? null,
                'isFeatured' => true,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Administrative building, swimming pool, and water treatment plant in Mondulkiri.',
                    'km' => 'អគាររដ្ឋបាល អាងហែលទឹក និងរោងចក្រប្រព្រឹត្តិកម្មទឹកស្អាតក្នុងខេត្តមណ្ឌលគិរី។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Construction of a dam and fence</li><li>1800 square meter reaction tank</li><li>Raw water pumping station to the treatment site</li><li>Provincial standard water supply network</li><li>160KVA power system</li><li>Water meters, supply to 1000 houses</li></ul>',
                    'km' => '<ul><li>ការសាងសង់ទំនប់ និងរបង</li><li>អាងចម្រោះទំហំ ១,៨០០ ម៉ែត្រការ៉េ</li><li>ស្ថានីយ៍បូមទឹកឆៅទៅមជ្ឈមណ្ឌលប្រព្រឹត្តិកម្ម</li><li>បណ្តាញផ្គត់ផ្គង់ទឹកស្អាតតាមស្តង់ដារខេត្ត</li><li>ប្រព័ន្ធអគ្គិសនី ១៦០KVA</li><li>នាឡិកាទឹក ផ្គត់ផ្គង់ដល់ ១,០០០ ផ្ទះ</li></ul>'
                ],
            ],
            // 18. EDC WAT PHNOM HEADQUARTERS
            [
                'title' => ['en' => 'EDC Wat Phnom Headquarters', 'km' => 'ទីស្នាក់ការកណ្តាល EDC វត្តភ្នំ'],
                'slug' => 'edc-headquarters',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'EDC',
                'scale' => '5 Floor | 6,762 m²',
                'timeline' => 'May 2013 - Nov 2014',
                'completionDate' => '2014-11-01',
                'heroImage' => $img['energy'],
                'project_category_id' => $cat['energy'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '5-floor administrative headquarters for Electricité du Cambodge.',
                    'km' => 'ទីស្នាក់ការរដ្ឋបាល ៥ ជាន់ សម្រាប់អគ្គិសនីកម្ពុជា។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Roofing and fencing work</li><li>Front dimensions 120cm 100cm 80cm, depth 18m</li><li>Generator system work, telephone, Ethernet, voice</li><li>Air conditioning system, fire protection system</li><li>2 elevators</li></ul>',
                    'km' => '<ul><li>ការងារដំបូល និងរបង</li><li>ទំហំខាងមុខ គ្រឹះជម្រៅ ១៨ម៉ែត្រ</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត និងសំឡេង</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ និងប្រព័ន្ធការពារអគ្គិភ័យ</li><li>ជណ្ដើរយន្ត ២</li></ul>'
                ],
            ],
            // 19. KRATIE WATER TREATMENT PLANT
            [
                'title' => ['en' => 'Kratie Water Treatment Plant', 'km' => 'រោងចក្រប្រព្រឹត្តិកម្មទឹកស្អាតក្រចេះ'],
                'slug' => 'kratie-water-plant',
                'location' => ['en' => 'Kratie, Cambodia', 'km' => 'ក្រចេះ, កម្ពុជា'],
                'client' => 'Provincial Water Authority',
                'scale' => 'Large scale infrastructure',
                'timeline' => 'Aug 2011 - Mar 2013',
                'completionDate' => '2013-03-01',
                'heroImage' => $img['water'],
                'project_category_id' => $cat['infrastructure'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Complex water treatment facility including intake towers and distribution network.',
                    'km' => 'មជ្ឈមណ្ឌលប្រព្រឹត្តិកម្មទឹកស្អាត រួមទាំងប៉មបូមទឹក និងបណ្តាញចែកចាយ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Design of the circle concrete intake tower station high 27m from Mekong river</li><li>Vertical Turbine Pumps (Four 120kw pumps, 600m3/h each)</li><li>Design of WTP (18,000m3/day), Reservoir (1200m3) and Water Tower (350m3, 30m high)</li><li>Design distribution network (288,500m of HDPE Pipe)</li><li>Drainage System, Fencing, Gabion Box Rip Rap</li><li>Administration, Chemical, Pump Room & SCADA control building</li></ul>',
                    'km' => '<ul><li>ការរចនាប៉មបូមទឹកបេតុងកម្ពុស់ ២៧ម៉ែត្រ ពីទន្លេមេគង្គ</li><li>ម៉ូទ័របូមទឹក (៤ គ្រាប់ កម្លាំង ១២០គីឡូវ៉ាត់ក្នុងមួយគ្រាប់)</li><li>រោងចក្រប្រព្រឹត្តិកម្ម (១៨,០០០ ម៉ែត្រគូបក្នុងមួយថ្ងៃ) និងអាងស្តុកទឹក</li><li>បណ្តាញចែកចាយបំពង់ HDPE ប្រវែង ២៨៨,៥០០ ម៉ែត្រ</li><li>ប្រព័ន្ធលូ របង និងការងារការពារច្រាំងទន្លេ</li><li>អគាររដ្ឋបាល មន្ទីរពិសោធន៍ ស្ថានីយ៍បូម និងប្រព័ន្ធបញ្ជា SCADA</li></ul>'
                ],
            ],
            // 20. AL SERKAL MOSQUE AND ANCILLARY UNIT
            [
                'title' => ['en' => 'Al Serkal Mosque and Ancillary Unit', 'km' => 'វិហារអាល់សឺកាល់ និងអង្គភាពពាក់ព័ន្ធ'],
                'slug' => 'al-serkal-mosque',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភ្នំពេញ, កម្ពុជា'],
                'client' => 'Al Serkal',
                'scale' => 'Religious Facility',
                'timeline' => 'Mar 2012 - Jun 2013',
                'completionDate' => '2013-06-01',
                'heroImage' => $img['mosque'],
                'project_category_id' => $cat['religious'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Construction of the landmark Al Serkal Mosque and supporting facilities.',
                    'km' => 'ការសាងសង់វិហារឥស្លាម អាល់សឺកាល់ និងអគារជំនួយផ្សេងៗ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>General Works, Civil Works</li><li>Mechanical Works, Electrical and Plumbing Works</li><li>Landscaping and Finishing Work</li><li>Air-Conditioner RVC</li><li>External Works, Asphalt Road</li></ul>',
                    'km' => '<ul><li>ការងារទូទៅ និងការងារស៊ីវិល</li><li>ការងារមេកានិច អគ្គិសនី និងបណ្តាញទឹក</li><li>ការតុបតែងសោភ័ណភាព និងការងារបញ្ចប់</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ RVC</li><li>ការងារខាងក្រៅ និងផ្លូវបេតុងសរ</li></ul>'
                ],
            ],
            // 21. KOICA BUILDING
            [
                'title' => ['en' => 'KOICA Building', 'km' => 'អគារភ្នាក់ងារសហប្រតិបត្តិការអន្តរជាតិកូរ៉េ (KOICA)'],
                'slug' => 'koica-building',
                'location' => ['en' => 'Phnom Penh, Cambodia', 'km' => 'ភំ្នពេញ, កម្ពុជា'],
                'client' => 'KOICA',
                'scale' => '4 Floor | 4,500 m²',
                'timeline' => 'Dec 2011 - Dec 2012',
                'completionDate' => '2012-12-01',
                'heroImage' => $img['gov'],
                'project_category_id' => $cat['government'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => '4-floor office building for the Korea International Cooperation Agency.',
                    'km' => 'អគារការិយាល័យ ៤ ជាន់ សម្រាប់ភ្នាក់ងារសហប្រតិបត្តិការអន្តរជាតិកូរ៉េ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Steel and fence work</li><li>Front dimensions 120cm 100cm 80cm, depth 18m</li><li>Building A reinforced concrete 4 floors</li><li>Generator, telephone, Ethernet, voice system work</li><li>Air conditioning system, oxygen system work</li><li>1 elevator</li></ul>',
                    'km' => '<ul><li>ការងារដែក និងរបង</li><li>ទំហំខាងមុខ ១២០សង់ទីម៉ែត្រ x ១០០សង់ទីម៉ែត្រ x ៨០សង់ទីម៉ែត្រ គ្រឹះជម្រៅ ១៨ម៉ែត្រ</li><li>អគារ A បេតុងអាមេ ៤ ជាន់</li><li>ម៉ាស៊ីនភ្លើង ទូរស័ព្ទ អ៊ីនធឺណិត និងសំឡេង</li><li>ប្រព័ន្ធម៉ាស៊ីនត្រជាក់ និងប្រព័ន្ធអុកស៊ីហ្សែន</li><li>ជណ្ដើរយន្ត ១</li></ul>'
                ],
            ],
            // 22. TAKAVIT CLINIC
            [
                'title' => ['en' => 'Takavit Clinic', 'km' => 'គ្លីនិកតាកាវីត'],
                'slug' => 'takavit-clinic',
                'location' => ['en' => 'Cambodia', 'km' => 'កម្ពុជា'],
                'client' => 'NGO',
                'scale' => '190 m²',
                'timeline' => 'Oct 2011 - Oct 2012',
                'completionDate' => '2012-10-01',
                'heroImage' => $img['health'],
                'project_category_id' => $cat['healthcare'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Healthcare clinic facility including consultation and treatment rooms.',
                    'km' => 'មណ្ឌលសុខភាព ដែលរួមមានបន្ទប់ពិគ្រោះយោបល់ និងបន្ទប់ព្យាបាល។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Entrance/Consultation, office + vaccination</li><li>Consultation room 1 & 2</li><li>Minor operation and treatment</li><li>Delivery room, waiting and recovery rooms</li><li>Patient wards 1, 2 & 3 with bath and latrine</li><li>Kitchen and solar hot water system</li><li>Pharmacy and staff room</li></ul>',
                    'km' => '<ul><li>ច្រកចូល/ការពិគ្រោះ ការិយាល័យ និងការចាក់វ៉ាក់សាំង</li><li>បន្ទប់ពិគ្រោះយោបល់ ១ និង ២</li><li>ការវះកាត់តូច និងការព្យាបាល</li><li>បន្ទប់សម្រាលកូន បន្ទប់រង់ចាំ និងបន្ទប់សម្រាក</li><li>បន្ទប់អ្នកជំងឺ ១, ២ និង ៣ ជាមួយបន្ទប់ទឹក</li><li>ផ្ទះបាយ និងប្រព័ន្ធទឹកក្តៅដើរដោយថាមពលព្រះអាទិត្យ</li><li>ឱសថស្ថាន និងបន្ទប់បុគ្គលិក</li></ul>'
                ],
            ],
            // 23. DAMBE CLINIC
            [
                'title' => ['en' => 'Dambe Clinic', 'km' => 'មណ្ឌលសុខភាពដំបែ'],
                'slug' => 'dambe-clinic',
                'location' => ['en' => 'Dambe, Cambodia', 'km' => 'ដំបែ, កម្ពុជា'],
                'client' => 'NGO',
                'scale' => '80 m²',
                'timeline' => 'Oct 2010 - Oct 2011',
                'completionDate' => '2011-10-01',
                'heroImage' => $img['health'],
                'project_category_id' => $cat['healthcare'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Small-scale health clinic supporting local community health services in Dambe.',
                    'km' => 'មណ្ឌលសុខភាពខ្នាតតូច សម្រាប់គាំទ្រសេវាសុខភាពសហគមន៍មូលដ្ឋានក្នុងស្រុកដំបែ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Entrance and vaccination room</li><li>Birthing and delivery rooms</li><li>Consultation room and solar hot water system</li><li>Latrine construction</li></ul>',
                    'km' => '<ul><li>ច្រកចូល និងបន្ទប់ចាក់វ៉ាក់សាំង</li><li>បន្ទប់សម្រាលកូន</li><li>បន្ទប់ពិគ្រោះយោបល់ និងប្រព័ន្ធទឹកក្តៅដើរដោយថាមពលព្រះអាទិត្យ</li><li>ការសាងសង់បង្គន់អនាម័យ</li></ul>'
                ],
            ],
            // 24. CHANG HOP SCHOOL
            [
                'title' => ['en' => 'Chang Hop School', 'km' => 'សាលាបឋមសិក្សាជាងហប់'],
                'slug' => 'chang-hop-school',
                'location' => ['en' => 'Cambodia', 'km' => 'កម្ពុជា'],
                'client' => 'NGO',
                'scale' => '3 Classrooms',
                'timeline' => 'Oct 2010 - Oct 2011',
                'completionDate' => '2011-10-01',
                'heroImage' => $img['school'],
                'project_category_id' => $cat['education'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Primary school building with three classrooms and a teacher\'s room.',
                    'km' => 'អគារសាលាបឋមសិក្សា ដែលមាន ៣ បន្ទប់រៀន និងបន្ទប់គ្រូ ១។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Three classrooms construction</li><li>One teacher\'s room</li><li>Electrical works and internal decoration</li><li>School furniture installation</li></ul>',
                    'km' => '<ul><li>ការសាងសង់បន្ទប់រៀន ៣</li><li>បន្ទប់គ្រូ ១</li><li>ការងារអគ្គិសនី និងការតុបតែងខាងក្នុង</li><li>ការដំឡើងគ្រឿងសង្ហារឹមសាលារៀន</li></ul>'
                ],
            ],
            // 25. SREY TRENG SCHOOL
            [
                'title' => ['en' => 'Srey Treng School', 'km' => 'សាលាបឋមសិក្សាស្រីត្រែង'],
                'slug' => 'srey-treng-school',
                'location' => ['en' => 'Cambodia', 'km' => 'កម្ពុជា'],
                'client' => 'NGO',
                'scale' => '3 Classrooms',
                'timeline' => 'Oct 2010 - Oct 2011',
                'completionDate' => '2011-10-01',
                'heroImage' => $img['school'],
                'project_category_id' => $cat['education'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Primary school building contributing to rural education infrastructure.',
                    'km' => 'អគារសាលាបឋមសិក្សា ដែលរួមចំណែកដល់ហេដ្ឋារចនាសម្ព័ន្ធអប់រំនៅជនបទ។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Three classrooms construction</li><li>One teacher\'s room</li><li>Electrical works and internal decoration</li><li>School furniture installation</li></ul>',
                    'km' => '<ul><li>ការសាងសង់បន្ទប់រៀន ៣</li><li>បន្ទប់គ្រូ ១</li><li>ការងារអគ្គិសនី និងការតុបតែងខាងក្នុង</li><li>ការដំឡើងគ្រឿងសង្ហារឹមសាលារៀន</li></ul>'
                ],
            ],
            // 26. CHHOUK MEAS CLINIC
            [
                'title' => ['en' => 'Chhouk Meas Clinic', 'km' => 'គ្លីនិកឈូកមាស'],
                'slug' => 'chhouk-meas-clinic',
                'location' => ['en' => 'Cambodia', 'km' => 'កម្ពុជា'],
                'client' => 'NGO',
                'scale' => '208 m²',
                'timeline' => 'Sep 2009 - Sep 2010',
                'completionDate' => '2010-09-01',
                'heroImage' => $img['health'],
                'project_category_id' => $cat['healthcare'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Health clinic including specialized pediatric department rooms.',
                    'km' => 'គ្លីនិកសុខភាព រួមទាំងបន្ទប់ផ្នែកកុមារឯកទេស។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Pediatric Department implementation (Waiting, Consultation rooms)</li><li>Medical store and staff room</li><li>Electrical and water supply systems</li><li>Medical equipment supply and setup</li><li>Bath and latrine construction</li></ul>',
                    'km' => '<ul><li>ការអនុវត្តផ្នែកកុមារ (បន្ទប់រង់ចាំ និងពិគ្រោះ)</li><li>បន្ទប់ស្តុកវេជ្ជសាស្ត្រ និងបន្ទប់បុគ្គលិក</li><li>ប្រព័ន្ធទឹក និងភ្លើង</li><li>ការផ្គត់ផ្គង់ និងដំឡើងឧបករណ៍វេជ្ជសាស្ត្រ</li><li>ការសាងសង់បន្ទប់ទឹក និងបង្គន់</li></ul>'
                ],
            ],
            // 27. THMAR PUOK REFFERAL HOSPITAL
            [
                'title' => ['en' => 'Thmar Puok Refferal Hospital', 'km' => 'មន្ទីរពេទ្យបង្អែកថ្មពួក'],
                'slug' => 'thmar-puok-hospital',
                'location' => ['en' => 'Banteay Meanchey, Cambodia', 'km' => 'បន្ទាយមានជ័យ, កម្ពុជា'],
                'client' => 'Ministry of Health / NGO',
                'scale' => '275 m²',
                'timeline' => 'Sep 2009 - Sep 2010',
                'completionDate' => '2010-09-01',
                'heroImage' => $img['health'],
                'project_category_id' => $cat['healthcare'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Ground-floor referral hospital wing providing essential medical services.',
                    'km' => 'មន្ទីរពេទ្យបង្អែកជាន់ផ្ទាល់ដី ដែលផ្តល់សេវាវេជ្ជសាស្ត្រដ៏សំខាន់។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Pediatric Department wing (Waiting, Consultation, Treatment)</li><li>Kitchen and internal decoration</li><li>Electrical and water supply systems</li><li>Medical equipment supply and setup</li><li>Bath and latrine construction</li></ul>',
                    'km' => '<ul><li>ផ្នែកកុមារ (បន្ទប់រង់ចាំ ពិគ្រោះ និងព្យាបាល)</li><li>ផ្ទះបាយ និងការតុបតែងខាងក្នុង</li><li>ប្រព័ន្ធទឹក និងភ្លើង</li><li>ការផ្គត់ផ្គង់ និងដំឡើងឧបករណ៍វេជ្ជសាស្ត្រ</li><li>ការសាងសង់បន្ទប់ទឹក និងបង្គន់</li></ul>'
                ],
            ],
            // 28. SYA CLINIC
            [
                'title' => ['en' => 'SYA Clinic', 'km' => 'មណ្ឌលសុខភាពស្យា'],
                'slug' => 'sya-clinic',
                'location' => ['en' => 'Cambodia', 'km' => 'កម្ពុជា'],
                'client' => 'NGO',
                'scale' => '108 m²',
                'timeline' => 'Sep 2009 - Sep 2010',
                'completionDate' => '2010-09-01',
                'heroImage' => $img['health'],
                'project_category_id' => $cat['healthcare'] ?? null,
                'isFeatured' => false,
                'status' => ProjectStatus::COMPLETED,
                'description' => [
                    'en' => 'Clinic project including well drilling and elevated water storage system.',
                    'km' => 'គម្រោងគ្លីនិក រួមមានការខួងអណ្តូង និងប្រព័ន្ធស្តុកទឹកខ្ពស់។'
                ],
                'scopeContributions' => [
                    'en' => '<ul><li>Consultation and delivery rooms</li><li>Water supply (Well drilling)</li><li>Supply and installation of water tower (h=6m, 2000L tank)</li><li>Bath and latrine construction</li></ul>',
                    'km' => '<ul><li>បន្ទប់ពិគ្រោះ និងសម្រាលកូន</li><li>ការផ្គត់ផ្គង់ទឹក (ការខួងអណ្តូង)</li><li>ការផ្គត់ផ្គង់ និងដំឡើងប៉មទឹក (កម្ពស់ ៦ម៉ែត្រ ធុង ២០០០ លីត្រ)</li><li>ការសាងសង់បន្ទប់ទឹក និងបង្គន់</li></ul>'
                ],
            ],
        ];

        foreach ($projects as $projectData) {
            Project::updateOrCreate(
                ['slug' => $projectData['slug']],
                $projectData
            );
        }
    }
}
