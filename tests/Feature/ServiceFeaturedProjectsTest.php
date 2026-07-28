<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ServiceFeaturedProjectsTest extends TestCase
{
    public function test_service_details_only_uses_real_featured_projects(): void
    {
        $controller = File::get(app_path('Http/Controllers/ServiceController.php'));
        $view = File::get(resource_path('views/pages/services/show.blade.php'));

        $this->assertStringContainsString('Project::query()', $controller);
        $this->assertStringContainsString("where('isActive', true)", $controller);
        $this->assertStringContainsString('service_featured_projects_', $controller);
        $this->assertStringContainsString('@if ($featuredProjects !== [])', $view);
        $this->assertStringContainsString("route('projects.show', ['slug' => \$project['slug']])", $view);
        $this->assertStringNotContainsString('Vatthanak Capital Expansion', $view);
        $this->assertStringNotContainsString('Skyline Residences', $view);
    }
}
