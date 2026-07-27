<?php

namespace Tests\Feature;

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PartnerLogoFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_placeholder_partner_logos_use_a_bundled_fallback(): void
    {
        Partner::create([
            'name' => ['en' => 'MOI', 'km' => 'MOI'],
            'logoUrl' => 'partners/placeholder.png',
            'type' => 'CLIENT',
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        Cache::forget('home_partners_array_v3_en');

        $this->blade('<x-home.partners />')
            ->assertSee('src="/partners/1.png"', false)
            ->assertDontSee('partners/placeholder.png')
            ->assertSee('onerror="this.classList.add(\'hidden\'); this.nextElementSibling.classList.remove(\'hidden\');"', false)
            ->assertSee('grid-cols-2', false);
    }
}
