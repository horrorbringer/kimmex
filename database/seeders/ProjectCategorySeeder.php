<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Infrastructure', 'km' => 'ហេដ្ឋារចនាសម្ព័ន្ធ'],
                'slug' => 'infrastructure',
                'description' => ['en' => 'Public works and major infrastructure projects.', 'km' => 'គម្រោងការងារសាធារណៈ និងហេដ្ឋារចនាសម្ព័ន្ធសំខាន់ៗ។'],
            ],
            [
                'name' => ['en' => 'Government', 'km' => 'រដ្ឋាភិបាល'],
                'slug' => 'government',
                'description' => ['en' => 'Official government buildings and facilities.', 'km' => 'អគារ និងសម្ភារៈប្រើប្រាស់ផ្លូវការរបស់រដ្ឋាភិបាល។'],
            ],
            [
                'name' => ['en' => 'Commercial', 'km' => 'ពាណិជ្ជកម្ម'],
                'slug' => 'commercial',
                'description' => ['en' => 'Private sector commercial developments.', 'km' => 'ការអភិវឌ្ឍន៍ពាណិជ្ជកម្មវិស័យឯកជន។'],
            ],
        ];

        foreach ($categories as $category) {
            ProjectCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
