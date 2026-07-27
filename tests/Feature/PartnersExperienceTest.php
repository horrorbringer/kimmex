<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PartnersExperienceTest extends TestCase
{
    public function test_home_partners_use_a_clean_consistent_logo_grid(): void
    {
        $partners = File::get(resource_path('views/components/home/partners.blade.php'));

        $this->assertStringContainsString('lg:grid-cols-6', $partners);
        $this->assertStringContainsString('border-l border-t border-titan-navy/10', $partners);
        $this->assertStringContainsString('group-hover:scale-[1.04]', $partners);
        $this->assertStringNotContainsString('grayscale', $partners);
        $this->assertStringContainsString('$shouldUseMarquee = count($partners) > 12', $partners);
        $this->assertStringContainsString('partner-marquee-scroll', $partners);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $partners);
    }

    public function test_partner_table_has_logo_column_and_grouped_actions(): void
    {
        $table = File::get(app_path('Filament/Resources/Partners/Tables/PartnersTable.php'));

        $this->assertFileExists(public_path('images/partner-placeholder.svg'));
        $this->assertStringContainsString("ImageColumn::make('logoUrl')", $table);
        $this->assertStringContainsString('ActionGroup::make([', $table);
        $this->assertStringContainsString('Heroicon::EllipsisVertical', $table);
    }
}
