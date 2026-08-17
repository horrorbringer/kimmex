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
        $this->assertStringContainsString('prev !== null && {{ $index }} === current', $template);
        $this->assertStringContainsString('x-show="{{ $index }} === current || {{ $index }} === prev"', $template);
        $this->assertStringContainsString('@if($index !== 0) style="display: none;" @endif', $template);
        $this->assertStringContainsString('.home-hero-viewport {', $template);
        $homePageService = file_get_contents(app_path('Services/HomePageService.php'));
        $this->assertStringContainsString("PublicStorage::cloudinaryResponsiveSrcset(\$slide['image'])", $homePageService);
        $this->assertStringContainsString("srcset=\"{{ \$slide['srcset'] }}\"", $template);
        $this->assertStringContainsString('@media (min-width: 640px)', $template);
        $this->assertStringContainsString('$heroTitleSize = \\Illuminate\\Support\\Str::length($slide[\'title\']) > 48', $template);
        $this->assertStringNotContainsString(':class="{{ \\Illuminate\\Support\\Str::length($slide[\'title\'])', $template);

        Blade::compileString($template);

        $this->addToAssertionCount(1);
    }
}
