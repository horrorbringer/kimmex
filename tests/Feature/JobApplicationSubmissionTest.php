<?php

namespace Tests\Feature;

use App\Jobs\SendJobApplicationTelegramNotification;
use App\Support\PublicStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobApplicationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_application_stores_null_job_id(): void
    {
        Storage::fake(PublicStorage::diskName());
        Queue::fake();

        $response = $this->post(route('careers.apply'), [
            'job_id' => 'general-application',
            'full_name' => 'Alea Barnett',
            'email' => 'vosilyce@mailinator.com',
            'phone' => '+1 (262) 127-1285',
            'resume' => UploadedFile::fake()->create(
                'resume.docx',
                20,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ),
            'message' => 'Incidunt ea sed a a',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('job_applications', [
            'jobId' => null,
            'applicantName' => 'Alea Barnett',
            'email' => 'vosilyce@mailinator.com',
            'status' => 'PENDING',
        ]);

        Queue::assertPushed(SendJobApplicationTelegramNotification::class);
    }

    public function test_job_application_telegram_delivery_is_queued_after_the_submission_is_saved(): void
    {
        $controller = File::get(app_path('Http/Controllers/FormController.php'));
        $job = File::get(app_path('Jobs/SendJobApplicationTelegramNotification.php'));

        $this->assertStringContainsString('SendJobApplicationTelegramNotification::dispatch($application)->afterCommit()', $controller);
        $this->assertStringNotContainsString('new TelegramService', $controller);
        $this->assertStringContainsString('implements ShouldQueue', $job);
        $this->assertStringContainsString('public int $timeout = 25', $job);
        $this->assertStringContainsString('public array $backoff = [10, 60]', $job);
        $this->assertStringContainsString('notifyJobApplication([', $job);
    }
}
