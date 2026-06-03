<?php

namespace Tests\Feature;

use App\Support\PublicStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactInquirySubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_attachment_stores_on_public_uploads_disk(): void
    {
        Storage::fake(PublicStorage::diskName());

        $response = $this->post(route('contact.submit'), [
            'first_name' => 'Alea',
            'last_name' => 'Barnett',
            'email' => 'vosilyce@mailinator.com',
            'phone' => '+1 (262) 127-1285',
            'subject' => 'Project inquiry',
            'message' => 'Please review the attached file.',
            'attachment' => UploadedFile::fake()->create('brief.pdf', 20, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $inquiry = \App\Models\Inquiry::query()->firstOrFail();

        $this->assertNotNull($inquiry->attachment_url);
        Storage::disk(PublicStorage::diskName())->assertExists($inquiry->attachment_url);
    }
}
