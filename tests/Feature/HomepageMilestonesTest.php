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
        $this->assertStringContainsString('A legacy built milestone by milestone', $milestones);
        $this->assertStringContainsString("'image' => \\App\\Support\\PublicStorage::urlIfExists", $milestones);
        $this->assertStringContainsString('$roadPositions', $milestones);
        $this->assertStringContainsString("url('/about#milestones')", $milestones);
        $this->assertStringContainsString('id="milestones"', $aboutPage);
    }
}
