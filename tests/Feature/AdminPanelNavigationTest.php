<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminPanelNavigationTest extends TestCase
{
    public function test_the_unused_ai_switcher_is_not_rendered_in_the_admin_top_navigation(): void
    {
        $provider = File::get(app_path('Providers/Filament/AdminPanelProvider.php'));

        $this->assertStringNotContainsString("@livewire('ai-switcher')", $provider);
        $this->assertStringNotContainsString('PanelsRenderHook::GLOBAL_SEARCH_BEFORE', $provider);
    }
}
