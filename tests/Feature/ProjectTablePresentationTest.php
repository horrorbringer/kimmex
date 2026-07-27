<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProjectTablePresentationTest extends TestCase
{
    public function test_project_table_uses_a_default_thumbnail_when_a_project_has_no_hero_image(): void
    {
        $table = file_get_contents(app_path('Filament/Resources/Projects/Tables/ProjectsTable.php'));

        $this->assertFileExists(public_path('images/project-placeholder.svg'));
        $this->assertStringContainsString("->defaultImageUrl(asset('images/project-placeholder.svg'))", $table);
    }
}
