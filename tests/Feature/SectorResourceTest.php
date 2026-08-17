<?php

namespace Tests\Feature;

use App\Filament\Resources\Sectors\Pages\ListSectors;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class SectorResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_sectors_can_be_rendered_on_services_page(): void
    {
        app()->setLocale('en');

        Sector::create([
            'title' => ['en' => 'Aerospace Engineering', 'km' => 'វិស្វកម្មអវកាស'],
            'description' => ['en' => 'Aerospace construction facilities.', 'km' => 'សំណង់អវកាស។'],
            'icon' => 'lucide-plane',
            'image' => '/images/webp/projects/Thumbnail-1.webp',
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('Aerospace Engineering');
    }

    public function test_sectors_render_khmer_translation_on_khmer_locale(): void
    {
        app()->setLocale('km');

        Sector::create([
            'title' => ['en' => 'Aerospace Engineering', 'km' => 'វិស្វកម្មអវកាស'],
            'description' => ['en' => 'Aerospace construction facilities.', 'km' => 'សំណង់អវកាស។'],
            'icon' => 'lucide-plane',
            'image' => '/images/webp/projects/Thumbnail-1.webp',
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('វិស្វកម្មអវកាស');
    }

    public function test_admin_can_access_sector_resource_list(): void
    {
        $admin = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        $this->actingAs($admin);

        Livewire::test(ListSectors::class)
            ->assertSuccessful();
    }

    public function test_inactive_sectors_are_hidden_from_services_page(): void
    {
        app()->setLocale('en');

        Sector::create([
            'title' => ['en' => 'Hidden Secret Sector', 'km' => 'វិស័យសម្ងាត់'],
            'icon' => 'lucide-lock',
            'image' => '/images/webp/projects/Thumbnail-1.webp',
            'orderIndex' => 1,
            'isActive' => false,
        ]);

        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertDontSee('Hidden Secret Sector');
    }
}
