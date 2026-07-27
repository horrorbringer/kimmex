<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProjectEditorWorkflowTest extends TestCase
{
    public function test_project_editor_uses_a_clear_four_step_workflow(): void
    {
        $form = File::get(app_path('Filament/Resources/Projects/Schemas/ProjectForm.php'));

        $this->assertStringContainsString('Wizard::make([', $form);
        $this->assertStringContainsString("Step::make(__('1. Project Basics'))", $form);
        $this->assertStringContainsString("Step::make(__('2. Project Story'))", $form);
        $this->assertStringContainsString("Step::make(__('3. Photos'))", $form);
        $this->assertStringContainsString("Step::make(__('4. Review & Publish'))", $form);
        $this->assertStringContainsString("Textarea::make('scopeContributions')", $form);
        $this->assertStringContainsString('one contribution per line', $form);
        $this->assertStringContainsString("persistStepInQueryString('project-step')", $form);
    }
}
