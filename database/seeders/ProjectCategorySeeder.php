<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Infrastructure', 'km' => 'ហេដ្ឋារចនាសម្ព័ន្ធ'],
                'slug' => 'infrastructure',
                'description' => ['en' => 'Public works, water treatment, and major infrastructure projects.', 'km' => 'គម្រោងការងារសាធារណៈ ប្រព្រឹត្តិកម្មទឹក និងហេដ្ឋារចនាសម្ព័ន្ធសំខាន់ៗ។'],
            ],
            [
                'name' => ['en' => 'Government', 'km' => 'រដ្ឋាភិបាល'],
                'slug' => 'government',
                'description' => ['en' => 'Official government buildings and facilities.', 'km' => 'អគារ និងសម្ភារៈប្រើប្រាស់ផ្លូវការរបស់រដ្ឋាភិបាល។'],
            ],
            [
                'name' => ['en' => 'Commercial', 'km' => 'ពាណិជ្ជកម្ម'],
                'slug' => 'commercial',
                'description' => ['en' => 'Private sector commercial and industrial developments.', 'km' => 'ការអភិវឌ្ឍន៍ពាណិជ្ជកម្ម និងឧស្សាហកម្មវិស័យឯកជន។'],
            ],
            [
                'name' => ['en' => 'Healthcare', 'km' => 'សុខាភិបាល'],
                'slug' => 'healthcare',
                'description' => ['en' => 'Hospitals, clinics, and medical facilities.', 'km' => 'មន្ទីរពេទ្យ គ្លីនិក និងមណ្ឌលសុខភាព។'],
            ],
            [
                'name' => ['en' => 'Education', 'km' => 'អប់រំ'],
                'slug' => 'education',
                'description' => ['en' => 'Schools and educational facilities.', 'km' => 'សាលារៀន និងគ្រឹះស្ថានអប់រំ។'],
            ],
            [
                'name' => ['en' => 'Energy & Utilities', 'km' => 'ថាមពល និងប្រព័ន្ធសាធារណៈ'],
                'slug' => 'energy',
                'description' => ['en' => 'Electricity, power, and utility infrastructure.', 'km' => 'ហេដ្ឋារចនាសម្ព័ន្ធអគ្គិសនី ថាមពល និងប្រព័ន្ធសាធារណៈ។'],
            ],
            [
                'name' => ['en' => 'Religious & Cultural', 'km' => 'សាសនា និងវប្បធម៌'],
                'slug' => 'religious',
                'description' => ['en' => 'Mosques, temples, and cultural landmarks.', 'km' => 'វិហារ ព្រះវិហារ និងចំណុចសំខាន់វប្បធម៌។'],
            ],
        ];

        foreach ($categories as $category) {
            ProjectCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
