<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomepageMilestonesTest extends TestCase
{
    public function test_the_homepage_includes_a_visual_company_milestones_roadmap(): void
    {
        $homepage = File::get(resource_path('views/welcome.blade.php'));
        $milestones = File::get(resource_path('views/components/home/milestones.blade.php'));
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));
        $styles = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('<x-home.milestones />', $homepage);
        $this->assertStringContainsString("Cache::remember('home_milestones_'.\$locale", $milestones);
        $this->assertStringContainsString('milestone-road-gradient', $milestones);
        $this->assertStringContainsString('M80 84C215 35 410 50 470 160C535 278 410 340 300 420', $milestones);
        $this->assertStringContainsString('viewBox="0 0 48 60"', $milestones);
        $this->assertStringContainsString('home-milestone-route', $milestones);
        $this->assertStringContainsString('$roadHeight = max(1512, (count($milestones) * 168) + 144);', $milestones);
        $this->assertStringContainsString('style="height: {{ $roadHeight }}px"', $milestones);
        $this->assertStringContainsString('preserveAspectRatio="none"', $milestones);
        $this->assertStringNotContainsString('$roadPinOffsets', $milestones);
        $this->assertStringContainsString('home-milestone-road-flow', $milestones);
        $this->assertStringContainsString('home-milestone-pin', $milestones);
        $this->assertStringContainsString('IntersectionObserver', $milestones);
        $this->assertStringContainsString('milestone-pin-visible', $milestones);
        $this->assertStringContainsString('milestone-card-visible', $milestones);
        $this->assertStringContainsString('window.setTimeout(() => this.cardShown = true, this.reducedMotion ? 0 : 180)', $milestones);
        $this->assertStringContainsString('home-milestone-pin-ring', $milestones);
        $this->assertStringContainsString('A legacy built milestone by milestone', $milestones);
        $this->assertStringContainsString("'image' => \\App\\Support\\PublicStorage::urlIfExists", $milestones);
        $this->assertStringContainsString("'detail' => \$milestone->getTranslation('detailed_description'", $milestones);
        $this->assertStringContainsString("\$detail = \$milestone['detail'] ?? '';", $milestones);
        $this->assertStringContainsString("fill=\"{{ \$color['hex'] }}\"", $milestones);
        $this->assertStringContainsString('tilt($event)', $milestones);
        $this->assertStringContainsString('home-milestone-card', $milestones);
        $this->assertStringContainsString('lg:min-h-[112px]', $milestones);
        $this->assertStringContainsString('line-clamp-2', $milestones);
        $this->assertStringContainsString('background-color: {{ $color[\'hex\'] }}', $milestones);
        $this->assertStringContainsString('home-milestone-mobile-pin', $milestones);
        $this->assertStringContainsString('home-milestone-card-pin absolute -top-8 right-5', $milestones);
        $this->assertStringContainsString('home-milestone-card-wrap relative z-1', $milestones);
        $this->assertStringContainsString('home-milestone-card relative overflow-hidden', $milestones);
        $this->assertStringContainsString('home-milestone-card-interactive cursor-pointer', $milestones);
        $this->assertStringContainsString('role="dialog"', $milestones);
        $this->assertStringContainsString('openDetail($event)', $milestones);
        $this->assertStringContainsString('Read story', $milestones);
        $this->assertStringContainsString('.milestone-pin-waiting .home-milestone-mobile-pin', $styles);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $styles);
        $this->assertStringNotContainsString('@media (prefers-reduced-motion: reduce), (hover: none), (pointer: coarse)', $styles);
        $this->assertStringNotContainsString("shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'", $milestones);
        $this->assertStringContainsString("url('/about#milestones')", $milestones);
        $this->assertStringContainsString('id="milestones"', $aboutPage);
    }
}
