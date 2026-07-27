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

    public function test_project_editor_does_not_show_the_ai_auto_fill_action(): void
    {
        $createPage = File::get(app_path('Filament/Resources/Projects/Pages/CreateProject.php'));
        $editPage = File::get(app_path('Filament/Resources/Projects/Pages/EditProject.php'));

        $this->assertStringNotContainsString("AIHelper::getAutoFillAction('project')", $createPage);
        $this->assertStringNotContainsString("AIHelper::getAutoFillAction('project')", $editPage);
    }

    public function test_project_editor_does_not_show_the_testimonial_request_action(): void
    {
        $editPage = File::get(app_path('Filament/Resources/Projects/Pages/EditProject.php'));

        $this->assertStringNotContainsString("Action::make('requestTestimonial')", $editPage);
        $this->assertStringNotContainsString('Request Testimonial', $editPage);
    }
}
