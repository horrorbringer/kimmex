<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProjectStatusFilterOptionsTest extends TestCase
{
    public function test_project_status_filter_only_includes_statuses_used_by_active_projects(): void
    {
        $controller = File::get(app_path('Http/Controllers/ProjectController.php'));

        $this->assertStringContainsString('$availableStatusValues = $allProjects', $controller);
        $this->assertStringContainsString('->map(fn (Project $project): ?string => $project->status?->value)', $controller);
        $this->assertStringContainsString('->filter(fn (ProjectStatus $status): bool => $availableStatusValues->contains($status->value))', $controller);
        $this->assertStringNotContainsString('collect(ProjectStatus::cases())->map(fn ($s)', $controller);
    }
}
