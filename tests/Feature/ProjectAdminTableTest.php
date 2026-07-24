<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProjectAdminTableTest extends TestCase
{
    public function test_projects_table_shows_a_hero_thumbnail_after_status_and_groups_record_actions(): void
    {
        $table = File::get(app_path('Filament/Resources/Projects/Tables/ProjectsTable.php'));

        $this->assertStringContainsString("TextColumn::make('status')", $table);
        $this->assertStringContainsString("ImageColumn::make('heroImage')", $table);
        $this->assertLessThan(
            strpos($table, "TextColumn::make('title')"),
            strpos($table, "ImageColumn::make('heroImage')"),
        );
        $this->assertStringContainsString('PublicStorage::urlIfExists($record->heroImage)', $table);
        $this->assertStringContainsString('ActionGroup::make([', $table);
        $this->assertStringContainsString('Heroicon::EllipsisVertical', $table);
        $this->assertStringContainsString('->iconButton()', $table);
    }
}
