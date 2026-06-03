<?php

namespace Tests\Feature;

use App\Support\PublicStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobApplicationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_application_stores_null_job_id(): void
    {
        Storage::fake(PublicStorage::diskName());

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
    }
}
