<?php

namespace Tests\Feature;

use App\Filament\Imports\EmployeeImporter;
use Tests\TestCase;

class EmployeeImportConfigurationTest extends TestCase
{
    public function test_employee_importer_offers_a_simple_csv_format(): void
    {
        $columns = EmployeeImporter::getColumns();

        $this->assertSame([
            'name',
            'role',
            'email',
            'phone',
            'location',
            'specialization',
            'experience',
            'is_active',
        ], array_map(fn ($column): string => $column->getName(), $columns));
    }

    public function test_employee_import_updates_existing_records_by_email_and_is_admin_only(): void
    {
        $importer = file_get_contents(app_path('Filament/Imports/EmployeeImporter.php'));
        $table = file_get_contents(app_path('Filament/Resources/Employees/Tables/EmployeesTable.php'));

        $this->assertStringContainsString("Employee::query()->firstOrNew([\n                'email' => \$email,", $importer);
        $this->assertStringContainsString("->where('role', 'ADMIN')->where('is_active', true)", $importer);
        $this->assertStringContainsString('->importer(EmployeeImporter::class)', $table);
        $this->assertStringContainsString('->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)', $table);
    }

    public function test_static_employee_import_example_matches_the_importer_columns(): void
    {
        $example = file_get_contents(public_path('employee-importer-example.csv'));

        $this->assertStringStartsWith(
            'name,role,email,phone,location,specialization,experience,is_active',
            $example,
        );
    }
}
