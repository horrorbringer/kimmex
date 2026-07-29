<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrgUnitEmployeeAutofillTest extends TestCase
{
    public function test_org_unit_form_fills_the_position_title_from_the_selected_employee(): void
    {
        $form = file_get_contents(app_path('Filament/Resources/OrgUnits/Schemas/OrgUnitForm.php'));

        $this->assertStringContainsString("Select::make('employeeId')", $form);
        $this->assertStringContainsString('->afterStateUpdated(function (Set $set, ?string $state): void {', $form);
        $this->assertStringContainsString("\$set('title', filled(\$employee->role) ? \$employee->role : \$employee->name);", $form);
        $this->assertStringContainsString("TextInput::make('title')", $form);
    }
}
