<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminTableActionMenuTest extends TestCase
{
    public function test_all_admin_list_tables_use_the_shared_three_dot_action_menu(): void
    {
        $tableFiles = glob(app_path('Filament/Resources/*/Tables/*Table.php'));

        $this->assertNotEmpty($tableFiles);

        foreach ($tableFiles as $tableFile) {
            $table = file_get_contents($tableFile);

            $this->assertStringContainsString('ActionGroup::make([', $table, $tableFile);
            $this->assertStringContainsString('Heroicon::EllipsisVertical', $table, $tableFile);
            $this->assertStringContainsString('->iconButton()', $table, $tableFile);
        }
    }
}
