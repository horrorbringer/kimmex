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

        $this->assertStringContainsString('<x-home.milestones />', $homepage);
        $this->assertStringContainsString("Cache::remember('home_milestones_'.\$locale", $milestones);
        $this->assertStringContainsString('milestone-road-gradient', $milestones);
        $this->assertStringContainsString('M80 84C215 35 410 50 470 160C535 278 410 340 300 420', $milestones);
        $this->assertStringContainsString('viewBox="0 0 48 60"', $milestones);
        $this->assertStringContainsString('home-milestone-route', $milestones);
        $this->assertStringContainsString('h-[1512px] w-[620px]', $milestones);
        $this->assertStringContainsString('$roadPinOffsets', $milestones);
        $this->assertStringContainsString('home-milestone-road-flow', $milestones);
        $this->assertStringContainsString('home-milestone-pin', $milestones);
        $this->assertStringContainsString('IntersectionObserver', $milestones);
        $this->assertStringContainsString('milestone-route-visible', $milestones);
        $this->assertStringContainsString('home-milestone-pin-ring', $milestones);
        $this->assertStringContainsString('A legacy built milestone by milestone', $milestones);
        $this->assertStringContainsString("'image' => \\App\\Support\\PublicStorage::urlIfExists", $milestones);
        $this->assertStringContainsString("fill=\"{{ \$color['hex'] }}\"", $milestones);
        $this->assertStringContainsString('tilt($event)', $milestones);
        $this->assertStringContainsString('home-milestone-card', $milestones);
        $this->assertStringContainsString('lg:min-h-[112px]', $milestones);
        $this->assertStringContainsString('line-clamp-2', $milestones);
        $this->assertStringContainsString('background-color: {{ $color[\'hex\'] }}', $milestones);
        $this->assertStringContainsString('home-milestone-pin-wrap absolute left-1/2 top-1/2 z-0', $milestones);
        $this->assertStringContainsString('home-milestone-card relative z-10', $milestones);
        $this->assertStringNotContainsString("shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'", $milestones);
        $this->assertStringContainsString("url('/about#milestones')", $milestones);
        $this->assertStringContainsString('id="milestones"', $aboutPage);
    }
}
