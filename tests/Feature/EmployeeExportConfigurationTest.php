<?php

namespace Tests\Feature;

use App\Filament\Exports\EmployeeExporter;
use Tests\TestCase;

class EmployeeExportConfigurationTest extends TestCase
{
    public function test_employee_exporter_offers_expected_columns(): void
    {
        $columns = EmployeeExporter::getColumns();

        $this->assertSame([
            'name',
            'role',
            'email',
            'phone',
            'location',
            'specialization',
            'experience',
            'image',
            'isActive',
            'orgUnit.title',
        ], array_map(fn ($column): string => $column->getName(), $columns));
    }

    public function test_employee_table_has_export_action_and_bulk_export_action(): void
    {
        $table = file_get_contents(app_path('Filament/Resources/Employees/Tables/EmployeesTable.php'));

        $this->assertStringContainsString('->exporter(EmployeeExporter::class)', $table);
        $this->assertStringContainsString('ExportAction::make(\'exportEmployees\')', $table);
        $this->assertStringContainsString('ExportBulkAction::make()', $table);
    }
}
