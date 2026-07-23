<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomepageHoverInteractionTest extends TestCase
{
    public function test_homepage_feature_cards_use_consistent_smooth_hover_transitions(): void
    {
        $aboutTemplate = File::get(resource_path('views/components/home/about.blade.php'));
        $projectsTemplate = File::get(resource_path('views/components/home/projects.blade.php'));
        $newsTemplate = File::get(resource_path('views/components/home/news.blade.php'));
        $servicesTemplate = File::get(resource_path('views/components/home/services.blade.php'));
        $testimonialsTemplate = File::get(resource_path('views/components/home/testimonials.blade.php'));
        $ctaTemplate = File::get(resource_path('views/components/home/cta.blade.php'));

        $this->assertSame(3, substr_count($aboutTemplate, 'hover:scale-[1.03] transition-transform duration-700 ease-out'));
        $this->assertSame(2, substr_count($projectsTemplate, 'group-hover:scale-[1.03] transition-transform duration-700 ease-out'));
        $this->assertStringContainsString('transition-shadow duration-500 ease-out', $newsTemplate);
        $this->assertStringContainsString('group-hover:translate-x-1 motion-reduce:transform-none', $newsTemplate);
        $this->assertStringContainsString('transition-[border-color,box-shadow] duration-300 ease-out', $servicesTemplate);
        $this->assertStringNotContainsString('hover:-translate', $projectsTemplate.$newsTemplate.$servicesTemplate.$testimonialsTemplate.$ctaTemplate);
        $this->assertStringContainsString('transition-all duration-700 ease-out motion-reduce:transition-none', $projectsTemplate);
    }
}
