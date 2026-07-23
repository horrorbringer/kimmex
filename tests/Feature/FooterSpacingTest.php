<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FooterSpacingTest extends TestCase
{
    public function test_the_footer_uses_a_compact_spacing_scale(): void
    {
        $footerTemplate = File::get(resource_path('views/components/footer.blade.php'));

        $this->assertStringContainsString('padding: 2.5rem 1.5rem 1.25rem;', $footerTemplate);
        $this->assertStringContainsString('gap: 2rem;', $footerTemplate);
        $this->assertStringContainsString('margin-top: 1.75rem;', $footerTemplate);
        $this->assertStringContainsString('padding: 1.25rem 0;', $footerTemplate);
    }
}
