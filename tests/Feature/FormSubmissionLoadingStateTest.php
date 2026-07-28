<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FormSubmissionLoadingStateTest extends TestCase
{
    public function test_submission_loading_only_starts_after_a_valid_form_submit(): void
    {
        $careerDetail = File::get(resource_path('views/pages/careers/show.blade.php'));
        $careerIndex = File::get(resource_path('views/pages/careers.blade.php'));
        $contact = File::get(resource_path('views/pages/contact.blade.php'));

        foreach ([$careerDetail, $careerIndex, $contact] as $view) {
            $this->assertStringContainsString('x-data="{ submitting: false }" x-on:submit="submitting = true"', $view);
            $this->assertStringNotContainsString('x-on:click="submitting = true"', $view);
        }
    }
}
