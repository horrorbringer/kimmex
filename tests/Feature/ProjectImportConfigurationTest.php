<?php

namespace Tests\Feature;

use App\Filament\Imports\ProjectImporter;
use Tests\TestCase;

class ProjectImportConfigurationTest extends TestCase
{
    public function test_project_importer_offers_the_expected_csv_columns(): void
    {
        $columns = ProjectImporter::getColumns();

        $this->assertSame([
            'title',
            'slug',
            'category',
            'status',
            'location',
            'client',
            'timeline',
            'scale',
            'completion_date',
            'hero_image_url',
            'description',
            'background',
            'objectives',
            'scope_of_work',
            'design_concept',
            'engineering_notes',
            'is_featured',
            'is_active',
        ], array_map(fn ($column): string => $column->getName(), $columns));
    }

    public function test_project_import_matches_existing_records_by_slug_and_is_admin_only(): void
    {
        $importer = file_get_contents(app_path('Filament/Imports/ProjectImporter.php'));
        $table = file_get_contents(app_path('Filament/Resources/Projects/Tables/ProjectsTable.php'));

        $this->assertStringContainsString("Project::query()->firstOrNew([\n            'slug' => \$this->data['slug'],", $importer);
        $this->assertStringContainsString("->where('role', 'ADMIN')->where('is_active', true)", $importer);
        $this->assertStringContainsString('->importer(ProjectImporter::class)', $table);
        $this->assertStringContainsString('->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)', $table);
    }

    public function test_static_project_import_example_matches_the_importer_columns(): void
    {
        $example = file_get_contents(public_path('project-importer-example.csv'));

        $this->assertStringStartsWith(
            'title,slug,category,status,location,client,timeline,scale,completion_date,hero_image_url,description,background,objectives,scope_of_work,design_concept,engineering_notes,is_featured,is_active',
            $example,
        );
    }
}
