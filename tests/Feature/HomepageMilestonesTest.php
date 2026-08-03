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
        $dialog = File::get(resource_path('views/components/home/milestone-dialog.blade.php'));
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));
        $styles = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('<x-home.milestones />', $homepage);
        $this->assertStringContainsString("Cache::remember('home_milestones_'.\$locale", $milestones);
        $this->assertStringContainsString('optimizedLocalImageUrl($milestone[\'image\'])', $milestones);
        $this->assertStringContainsString('cloudinaryResponsiveSrcset($milestone[\'image\'], [160, 320])', $milestones);
        $this->assertStringContainsString('sizes="(min-width: 1024px) 64px, 80px"', $milestones);
        $this->assertStringContainsString('milestone-road-gradient', $milestones);
        $this->assertStringContainsString('$roadStops = [];', $milestones);
        $this->assertStringContainsString('$roadWidth = max(1440, count($milestones) * 280);', $milestones);
        $this->assertStringContainsString('$roadStartX = 48;', $milestones);
        $this->assertStringContainsString('$roadEndX = $roadWidth - 48;', $milestones);
        $this->assertStringContainsString('$roadStops[] = [\'x\' => $x, \'y\' => $y, \'cardOffset\'', $milestones);
        $this->assertStringContainsString('$x = 280 + (($roadWidth - 560)', $milestones);
        $this->assertStringContainsString('home-milestone-route', $milestones);
        $this->assertStringContainsString('$roadHeight = 600;', $milestones);
        $this->assertStringContainsString('viewBox="0 0 {{ $roadWidth }} {{ $roadHeight }}"', $milestones);
        $this->assertStringContainsString('overflow-x-auto pb-6 lg:block', $milestones);
        $this->assertStringNotContainsString('scroll-smooth', $milestones);
        $this->assertStringNotContainsString('pt-[11rem]', $milestones);
        $this->assertStringNotContainsString('$roadPinOffsets', $milestones);
        $this->assertStringContainsString('home-milestone-road-flow', $milestones);
        $this->assertStringContainsString('home-milestone-station', $milestones);
        $this->assertStringContainsString('IntersectionObserver', $milestones);
        $this->assertStringContainsString('handleTimelineWheel(event)', $milestones);
        $this->assertStringContainsString('@wheel="handleTimelineWheel($event)"', $milestones);
        $this->assertStringNotContainsString('x-ref="desktopTimeline" @wheel=', $milestones);
        $this->assertStringContainsString('event.preventDefault();', $milestones);
        $this->assertStringContainsString('targetTimelineScrollLeft', $milestones);
        $this->assertStringContainsString('animateTimelineScroll()', $milestones);
        $this->assertStringContainsString('timeline.scrollLeft += distance * 0.32;', $milestones);
        $this->assertStringContainsString('x-ref="desktopTimeline"', $milestones);
        $this->assertStringNotContainsString('home-milestone-scroll-journey', $milestones);
        $this->assertStringContainsString('home-milestone-story-card', $milestones);
        $this->assertStringContainsString('A legacy built milestone by milestone', $milestones);
        $this->assertStringContainsString("'image' => \\App\\Support\\PublicStorage::urlIfExists", $milestones);
        $this->assertStringContainsString("'detail' => \$milestone->getTranslation('detailed_description'", $milestones);
        $this->assertStringContainsString("\$detail = \$milestone['detail'] ?? '';", $milestones);
        $this->assertStringContainsString('background-color: {{ $color }}', $milestones);
        $this->assertStringContainsString('home-milestone-mobile-track', $milestones);
        $this->assertStringContainsString('home-milestone-desktop-scroll', $milestones);
        $this->assertStringContainsString('w-[12.5rem]', $milestones);
        $this->assertStringContainsString('line-clamp-2 font-heading text-xs', $milestones);
        $this->assertStringContainsString('$loop->last', $milestones);
        $this->assertStringContainsString("{{ __('Latest') }}", $milestones);
        $this->assertStringNotContainsString('Scroll sideways to explore', $milestones);
        $this->assertStringContainsString('--milestone-delay: {{ $index * 110 }}ms', $milestones);
        $this->assertStringNotContainsString('startAutoScroll()', $milestones);
        $this->assertStringNotContainsString('@pointerdown="stopAutoScroll()"', $milestones);
        $this->assertStringNotContainsString('text-[11px] font-black leading-snug text-titan-navy {{ app()->getLocale() === \'km\' ? \'font-khmer text-xs\' : \'\' }}">{{ $milestone[\'title\'] }}</h3>', $milestones);
        $this->assertStringContainsString("@include('components.home.milestone-dialog'", $milestones);
        $this->assertStringContainsString('aria-label="{{ $milestone[\'title\'] }}"', $milestones);
        $this->assertStringContainsString("--card-y: {{ \$stop['cardOffset'] }}px", $milestones);
        $this->assertStringContainsString('role="dialog"', $dialog);
        $this->assertStringContainsString('openDetail($event)', $milestones);
        $this->assertStringContainsString('home-milestone-mobile-track::before', $styles);
        $this->assertStringContainsString('.home-milestone-desktop-scroll', $styles);
        $this->assertStringContainsString('.home-milestone-blueprint', $styles);
        $this->assertStringContainsString('home-milestone-card-in-desktop', $styles);
        $this->assertStringContainsString('home-milestone-marker-pulse', $styles);
        $this->assertStringContainsString('stroke-dasharray: 1;', $styles);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $styles);
        $this->assertStringNotContainsString('@media (prefers-reduced-motion: reduce), (hover: none), (pointer: coarse)', $styles);
        $this->assertStringNotContainsString("shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'", $milestones);
        $this->assertStringContainsString("url('/about#milestones')", $milestones);
        $this->assertStringContainsString('id="milestones"', $aboutPage);
    }
}
