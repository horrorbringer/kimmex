<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class JobPostingTablePresentationTest extends TestCase
{
    public function test_it_shows_the_department_name_and_groups_row_actions(): void
    {
        $table = File::get(app_path('Filament/Resources/JobPostings/Tables/JobPostingsTable.php'));

        $this->assertStringContainsString("TextColumn::make('department.name')", $table);
        $this->assertStringNotContainsString("TextColumn::make('departmentId')", $table);
        $this->assertStringContainsString('ActionGroup::make([', $table);
        $this->assertStringContainsString('Heroicon::EllipsisVertical', $table);
        $this->assertStringContainsString('->iconButton()', $table);
    }
}
