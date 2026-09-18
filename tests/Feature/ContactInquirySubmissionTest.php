<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Support\PublicStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactInquirySubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_attachment_stores_on_public_uploads_disk(): void
    {
        Http::fake();
        Queue::fake();
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

        $inquiry = Inquiry::query()->firstOrFail();

        $this->assertNotNull($inquiry->attachment_url);
        Storage::disk(PublicStorage::diskName())->assertExists($inquiry->attachment_url);
    }

    public function test_honeypot_silently_drops_submission(): void
    {
        Queue::fake();

        $response = $this->post(route('contact.submit'), [
            'website_url' => 'http://spambot.xyz',
            'first_name' => 'Harry',
            'last_name' => 'Bot',
            'email' => 'bot@spammer.com',
            'subject' => 'Hello',
            'message' => 'Spam message',
        ]);

        $response->assertRedirect();
        $this->assertSame(0, Inquiry::count());
    }

    public function test_time_trap_silently_drops_rapid_submission(): void
    {
        Queue::fake();

        $response = $this->post(route('contact.submit'), [
            '_form_time' => encrypt(time()), // 0 seconds elapsed
            'first_name' => 'Fast',
            'last_name' => 'Bot',
            'email' => 'fastbot@spammer.com',
            'subject' => 'Hello',
            'message' => 'Spam submitted in 0 seconds',
        ]);

        $response->assertRedirect();
        $this->assertSame(0, Inquiry::count());
    }

    public function test_spam_keywords_and_links_are_silently_dropped(): void
    {
        Queue::fake();

        // Exact spam payload received by user
        $response = $this->post(route('contact.submit'), [
            '_form_time' => encrypt(time() - 15),
            'first_name' => 'HarryNetQE',
            'last_name' => 'HarryNetQE',
            'email' => 'vduran47@yahoo.com',
            'subject' => 'YOU COULD WIN THE PREMIUM LAMBORGHINI AVENTADOR',
            'message' => 'THE LAMBORGHINI AVENTADOR IS THE PRIZE YOU DESERVE https://telegra.ph/Win-a-new-Lamborghini-Aventador-today-Message-ID-747189-09-14',
        ]);

        $response->assertRedirect();
        $this->assertSame(0, Inquiry::count());
    }

    public function test_cyrillic_spam_is_silently_dropped(): void
    {
        Queue::fake();

        $response = $this->post(route('contact.submit'), [
            '_form_time' => encrypt(time() - 10),
            'first_name' => 'Иван',
            'last_name' => 'Иванов',
            'email' => 'ivan@mail.ru',
            'subject' => 'Предложение о сотрудничестве',
            'message' => 'Привет, посмотрите наш сайт.',
        ]);

        $response->assertRedirect();
        $this->assertSame(0, Inquiry::count());
    }

    public function test_legitimate_submission_with_valid_form_time_succeeds(): void
    {
        Queue::fake();

        $response = $this->post(route('contact.submit'), [
            '_form_time' => encrypt(time() - 10), // 10 seconds elapsed
            'first_name' => 'Sopheap',
            'last_name' => 'Chan',
            'email' => 'sopheap.chan@example.com',
            'phone' => '+855 12 345 678',
            'subject' => 'Warehouse Construction Inquiry',
            'message' => 'We would like to request a quotation for our new factory.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame(1, Inquiry::count());
    }
}
