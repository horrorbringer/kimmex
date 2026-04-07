<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@kimmex.com']);
        $admin->forceFill([
            'name' => 'Admin User',
            'password' => 'password',
            'role' => 'ADMIN',
        ])->save();

        $editor = User::firstOrNew(['email' => 'editor@kimmex.com']);
        $editor->forceFill([
            'name' => 'Editor User',
            'password' => 'password',
            'role' => 'EDITOR',
        ])->save();

        $this->call([
            PartnerSeeder::class,
            OrganizationSeeder::class,
            BrandIdentitySeeder::class,
            ServicesTableSeeder::class,
            ProjectCategorySeeder::class,
            ProjectSeeder::class,
            TestimonialSeeder::class,
            DocumentSeeder::class,
            MilestoneSeeder::class,
            NewsArticleSeeder::class,
        ]);
    }
}
