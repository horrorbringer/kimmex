<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProjectFilterExperienceTest extends TestCase
{
    public function test_projects_page_uses_a_clear_responsive_filter_experience(): void
    {
        $template = File::get(resource_path('views/pages/projects/index.blade.php'));

        $this->assertStringContainsString('sticky top-24 z-30 mb-10 pt-2 lg:top-28', $template);
        $this->assertStringContainsString('overflow-hidden rounded-2xl border border-slate-200 bg-white', $template);
        $this->assertStringNotContainsString('shadow-titan-red/25', $template);
        $this->assertStringContainsString("{{ __('Filter Projects') }}", $template);
        $this->assertStringContainsString('lg:grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))]', $template);
        $this->assertStringContainsString('type="search" x-model="search"', $template);
        $this->assertStringContainsString("{{ __('Reset filters') }}", $template);
        $this->assertStringContainsString("{{ __('Active filters') }}", $template);
    }
}
