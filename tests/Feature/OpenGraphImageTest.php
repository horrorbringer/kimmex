<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OpenGraphImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_pages_render_an_absolute_open_graph_image_with_accessibility_metadata(): void
    {
        $image = 'https://res.cloudinary.com/indytvtu/image/upload/kimmex_website/projects/hero/example.webp.webp';
        $socialImage = 'https://res.cloudinary.com/indytvtu/image/upload/c_fill,g_auto,w_1200,h_630,f_jpg,q_auto/kimmex_website/projects/hero/example.webp.webp';
        Cache::put('has_public_documents', false);

        $html = Blade::render(
            '<x-layouts.app :title="$title" :description="$description" :image="$image" :image-alt="$imageAlt">Content</x-layouts.app>',
            [
                'title' => 'Example Project',
                'description' => 'Example project description.',
                'image' => $image,
                'imageAlt' => 'Example Project hero image',
            ],
        );

        $this->assertStringContainsString('<meta property="og:image" content="'.$socialImage.'">', $html);
        $this->assertStringContainsString('<meta property="og:image:secure_url" content="'.$socialImage.'">', $html);
        $this->assertStringContainsString('<meta property="og:image:type" content="image/jpeg">', $html);
        $this->assertStringContainsString('<meta property="og:image:width" content="1200">', $html);
        $this->assertStringContainsString('<meta property="og:image:height" content="630">', $html);
        $this->assertStringContainsString('<meta property="og:image:alt" content="Example Project hero image">', $html);
        $this->assertStringContainsString('<meta name="twitter:image" content="'.$socialImage.'">', $html);
        $this->assertStringContainsString('<meta name="twitter:image:alt" content="Example Project hero image">', $html);
    }
}
