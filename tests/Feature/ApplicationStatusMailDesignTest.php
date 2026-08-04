<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Mail\ApplicationStatusMail;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationStatusMailDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_status_email_renders_a_branded_responsive_status_update(): void
    {
        $mail = new ApplicationStatusMail(
            new JobApplication([
                'applicantName' => 'Sok Chan',
                'email' => 'sok.chan@example.com',
            ]),
            ApplicationStatus::INTERVIEW,
            'Please keep Wednesday afternoon available.',
        );

        $renderedMail = $mail->render();

        $this->assertStringContainsString('Career update', $renderedMail);
        $this->assertStringContainsString('Application update', $renderedMail);
        $this->assertStringContainsString('Interview Invitation', $renderedMail);
        $this->assertStringContainsString('Please keep Wednesday afternoon available.', $renderedMail);
        $this->assertStringContainsString('recruitment@kimmex.com.kh', $renderedMail);
        $this->assertStringContainsString('max-width: 600px', $renderedMail);
    }
}
