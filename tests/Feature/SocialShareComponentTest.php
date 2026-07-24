<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SocialShareComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_share_component_renders_styled_social_and_copy_link_buttons(): void
    {
        Cache::put('has_public_documents', false);

        $html = Blade::render(
            '<x-social-share :url="$url" :title="$title" />',
            [
                'url' => 'https://www.kimmex.com.kh/projects/example-project',
                'title' => 'Example Project',
            ],
        );

        $this->assertStringContainsString('background-color: #1877F2', $html);
        $this->assertStringContainsString('background-color: #0A66C2', $html);
        $this->assertStringContainsString('background-color: #0088cc', $html);
        $this->assertStringContainsString('baseStyle:', $html);
        $this->assertStringContainsString('background-color: #0B2B5C', $html);
        $this->assertStringContainsString('border-radius: 999px', $html);
        $this->assertStringContainsString(':style="copied ? copiedStyle : baseStyle"', $html);
        $this->assertStringContainsString(__('Copy Link'), $html);
        $this->assertStringContainsString('navigator.clipboard', $html);
    }
}
