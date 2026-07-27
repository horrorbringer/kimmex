<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProjectDetailExperienceTest extends TestCase
{
    public function test_project_details_use_a_clean_editorial_case_study_layout(): void
    {
        $view = File::get(resource_path('views/pages/projects/show.blade.php'));

        $this->assertStringContainsString("['label' => __('Client')", $view);
        $this->assertStringContainsString("__('Project Story')", $view);
        $this->assertStringContainsString('id="project-gallery"', $view);
        $this->assertStringNotContainsString('activeStory', $view);
        $this->assertStringNotContainsString("__('Project actions')", $view);
        $this->assertStringContainsString('project-scope-content', $view);
    }
}
