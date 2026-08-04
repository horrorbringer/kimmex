<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HeaderNavigationExperienceTest extends TestCase
{
    public function test_primary_navigation_uses_livewire_navigate_to_keep_the_header_and_styles_loaded(): void
    {
        $header = File::get(resource_path('views/components/header.blade.php'));
        $scripts = File::get(resource_path('js/app.js'));

        $this->assertGreaterThanOrEqual(13, substr_count($header, 'wire:navigate'));
        $this->assertStringContainsString('href="/" wire:navigate', $header);
        $this->assertStringContainsString('href="/about" wire:navigate', $header);
        $this->assertStringContainsString('href="/services" wire:navigate', $header);
        $this->assertStringContainsString('href="/projects" wire:navigate', $header);
        $this->assertStringContainsString('href="/news" wire:navigate', $header);
        $this->assertStringContainsString('href="/careers" wire:navigate', $header);
        $this->assertStringContainsString('href="/contact" wire:navigate', $header);
        $this->assertStringContainsString("document.addEventListener('livewire:navigate'", $scripts);
        $this->assertStringContainsString("document.addEventListener('livewire:navigated'", $scripts);
    }

    public function test_services_navigation_uses_the_configured_service_order(): void
    {
        $header = File::get(resource_path('views/components/header.blade.php'));
        $footer = File::get(resource_path('views/components/footer.blade.php'));

        $this->assertSame(1, substr_count($header, "->orderBy('orderIndex')"));
        $this->assertSame(1, substr_count($footer, "->orderBy('orderIndex')"));
        $this->assertStringNotContainsString("->sortBy(fn(\$svc) => \$svc->getTranslation('title', \$lang))", $header);
        $this->assertStringNotContainsString("->sortBy(fn(\$svc) => \$svc->getTranslation('title', \$lang))", $footer);
    }
}
