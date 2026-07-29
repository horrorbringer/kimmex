<?php

namespace Tests\Feature;

use Tests\TestCase;

class EmployeeAdminExperienceTest extends TestCase
{
    public function test_employee_admin_form_guides_editors_through_the_profile(): void
    {
        $form = file_get_contents(app_path('Filament/Resources/Employees/Schemas/EmployeeForm.php'));

        $this->assertStringContainsString("__('Start here. Only the employee name and job title are required.')", $form);
        $this->assertStringContainsString("__('Show on organization chart')", $form);
        $this->assertStringContainsString("__('Optional. Use this only when the employee also has an admin account.')", $form);
        $this->assertSame(4, substr_count($form, "->hiddenOn('create')"));
    }

    public function test_employee_table_has_a_clear_photo_visibility_and_empty_state(): void
    {
        $table = file_get_contents(app_path('Filament/Resources/Employees/Tables/EmployeesTable.php'));

        $this->assertStringContainsString("ImageColumn::make('image')", $table);
        $this->assertStringContainsString("TernaryFilter::make('isActive')", $table);
        $this->assertStringContainsString("->emptyStateHeading(__('No employees yet'))", $table);
        $this->assertFileExists(public_path('images/employee-placeholder.svg'));
    }
}
