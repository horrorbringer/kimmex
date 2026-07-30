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
        $this->assertStringContainsString("->select(['year', 'title', 'description', 'detailed_description', 'image'])", $milestones);
        $this->assertStringContainsString('milestone-road-gradient', $milestones);
        $this->assertStringContainsString('H900C1050 920 1050 980 1090 980', $milestones);
        $this->assertStringContainsString('text-center shadow-[0_8px_14px_-7px_rgba(11,43,92,0.55)]', $milestones);
        $this->assertStringContainsString('home-milestone-route', $milestones);
        $this->assertStringContainsString('home-milestone-route relative mx-auto w-full', $milestones);
        $this->assertStringContainsString('home-milestone-mobile-track relative space-y-5', $milestones);
        $this->assertStringContainsString('pb-20 lg:pb-24', $milestones);
        $this->assertStringContainsString('$roadHeight = max(540, (int) ceil(count($milestones) / $yearsPerRoad) * 180);', $milestones);
        $this->assertStringContainsString('style="height: {{ $roadHeight }}px"', $milestones);
        $this->assertStringContainsString('preserveAspectRatio="none"', $milestones);
        $this->assertStringContainsString('viewBox="0 0 1140 1000"', $milestones);
        $this->assertStringNotContainsString('$roadPinOffsets', $milestones);
        $this->assertStringContainsString('home-milestone-road-flow', $milestones);
        $this->assertStringContainsString('home-milestone-road-path" pathLength="1"', $milestones);
        $this->assertStringNotContainsString('milestone-road-arrow', $milestones);
        $this->assertStringNotContainsString("__('The journey continues')", $milestones);
        $this->assertStringContainsString('home-milestone-pin', $milestones);
        $this->assertStringContainsString('IntersectionObserver', $milestones);
        $this->assertStringContainsString('destroy() {', $milestones);
        $this->assertStringContainsString('this.observer?.disconnect();', $milestones);
        $this->assertStringContainsString('milestone-pin-visible', $milestones);
        $this->assertStringContainsString('milestone-card-visible', $milestones);
        $this->assertStringContainsString('window.setTimeout(() => this.cardShown = true, this.reducedMotion ? 0 : 180)', $milestones);
        $this->assertStringContainsString('home-milestone-pin-ring', $milestones);
        $this->assertStringContainsString('A legacy built milestone by milestone', $milestones);
        $this->assertStringContainsString('<section class="overflow-visible', $milestones);
        $this->assertStringContainsString("'image' => \\App\\Support\\PublicStorage::urlIfExists", $milestones);
        $this->assertStringContainsString("'detail' => \$milestone->getTranslation('detailed_description'", $milestones);
        $this->assertStringContainsString("\$detail = \$milestone['detail'] ?? '';", $milestones);
        $this->assertStringContainsString("style=\"background-color: {{ \$color['hex'] }}\"", $milestones);
        $this->assertStringContainsString('previewOpen: false', $milestones);
        $this->assertStringContainsString('@mouseenter="previewOpen = true"', $milestones);
        $this->assertStringContainsString('x-show="previewOpen"', $milestones);
        $this->assertStringContainsString('bottom-[calc(100%+0.8rem)]', $milestones);
        $this->assertStringNotContainsString("'lg:top-[calc(100%+0.8rem)]'", $milestones);
        $this->assertStringContainsString('home-milestone-station', $milestones);
        $this->assertStringContainsString('home-milestone-card-pin home-milestone-pin', $milestones);
        $this->assertStringContainsString('line-clamp-2', $milestones);
        $this->assertStringContainsString('background-color: {{ $color[\'hex\'] }}', $milestones);
        $this->assertStringContainsString('$roadLanes = [', $milestones);
        $this->assertStringContainsString('$yearsPerRoad = 4;', $milestones);
        $this->assertStringContainsString('home-milestone-station-wrap relative z-10', $milestones);
        $this->assertStringContainsString('lg:pointer-events-none lg:absolute lg:inset-0', $milestones);
        $this->assertStringContainsString('lg:pointer-events-auto lg:absolute', $milestones);
        $this->assertStringContainsString('$roadLaneIndex = min(intdiv($index, $yearsPerRoad)', $milestones);
        $this->assertStringContainsString("style=\"--road-stop-x: {{ \$roadStop['x'] }}%; --road-stop-y: {{ \$roadStop['y'] }}%\"", $milestones);
        $this->assertStringContainsString('lg:left-[var(--road-stop-x)]', $milestones);
        $this->assertStringContainsString('role="dialog"', $milestones);
        $this->assertStringContainsString('openDetail($event)', $milestones);
        $this->assertStringContainsString('Read story', $milestones);
        $this->assertStringContainsString('.milestone-card-waiting .home-milestone-station', $styles);
        $this->assertStringContainsString('stroke-dasharray: 1;', $styles);
        $this->assertStringContainsString('.home-milestone-mobile-track::before', $styles);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $styles);
        $this->assertStringNotContainsString('@media (prefers-reduced-motion: reduce), (hover: none), (pointer: coarse)', $styles);
        $this->assertStringNotContainsString("shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'", $milestones);
        $this->assertStringContainsString("url('/about#milestones')", $milestones);
        $this->assertStringContainsString('id="milestones"', $aboutPage);
    }
}
