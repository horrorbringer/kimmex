<?php

namespace Tests\Feature;

use Tests\TestCase;

class GoogleSearchConsoleVerificationTest extends TestCase
{
    public function test_the_public_layout_contains_the_google_search_console_verification_tag(): void
    {
        $layout = file_get_contents(resource_path('views/components/layouts/app.blade.php'));

        $this->assertIsString($layout);
        $this->assertStringContainsString(
            '<meta name="google-site-verification" content="FNWYdR92oYLYxH7Tc7wkW8v6nhkNPGmcNnz9gSPVcLw">',
            $layout,
        );
    }
}
