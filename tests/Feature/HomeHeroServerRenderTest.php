<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class HomeHeroServerRenderTest extends TestCase
{
    public function test_initial_hero_slide_content_is_server_rendered(): void
    {
        $template = file_get_contents(resource_path('views/components/home/hero-carousel.blade.php'));

        $this->assertIsString($template);
        $this->assertStringContainsString('@foreach($slides as $index => $slide)', $template);
        $this->assertStringContainsString("{{ \$slide['title'] }}", $template);
        $this->assertStringNotContainsString('x-text="slide.title"', $template);

        Blade::compileString($template);

        $this->addToAssertionCount(1);
    }
}
