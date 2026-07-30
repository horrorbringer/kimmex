<?php

namespace Tests\Feature;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CareerTelegramQrExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_postings_can_store_a_role_specific_telegram_qr_image(): void
    {
        $careerDetailsPage = File::get(resource_path('views/pages/careers/show.blade.php'));
        $careerController = File::get(app_path('Http/Controllers/CareerController.php'));

        $jobPostingForm = File::get(app_path('Filament/Resources/JobPostings/Schemas/JobPostingForm.php'));
        $jobPostingModel = File::get(app_path('Models/JobPosting.php'));

        $this->assertStringContainsString("FileUpload::make('telegramQr')", $jobPostingForm);
        $this->assertStringContainsString("->directory('jobs/telegram-qr')", $jobPostingForm);
        $this->assertStringContainsString("'telegramQr'", $jobPostingModel);
        $this->assertStringContainsString("'telegramQr' => \$jobDb->telegramQr", $careerController);
        $this->assertStringContainsString('$telegramQrUrl = PublicStorage::urlIfExists', $careerController);
        $this->assertStringContainsString('Carbon::JUST_NOW', $careerController);
        $this->assertStringContainsString("\$job['postedAt'] ?? \$job['postedDate'] ?? now()", $careerController);
        $this->assertStringContainsString('@if($telegramQrUrl)', $careerDetailsPage);
        $this->assertStringContainsString("{{ __('Careers on Telegram') }}", $careerDetailsPage);
        $this->assertStringContainsString("{{ __('Please Join Us on Telegram') }}", $careerDetailsPage);
    }

    public function test_career_details_render_the_role_specific_telegram_qr_image(): void
    {
        JobPosting::create([
            'title' => ['en' => 'Site Engineer'],
            'slug' => 'site-engineer',
            'status' => JobPostingStatus::OPEN,
            'telegramQr' => 'https://example.com/site-engineer-telegram-qr.png',
        ]);

        $response = $this->get(route('careers.show', ['slug' => 'site-engineer']));

        $response
            ->assertOk()
            ->assertSee(__('Careers on Telegram'))
            ->assertSee('https://example.com/site-engineer-telegram-qr.png');
    }
}
