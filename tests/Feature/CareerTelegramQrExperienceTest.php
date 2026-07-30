<?php

namespace Tests\Feature;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\SystemSetting;
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
        $settingsPage = File::get(app_path('Filament/Pages/ManageSettings.php'));

        $this->assertStringContainsString("FileUpload::make('telegramQr')", $jobPostingForm);
        $this->assertStringContainsString("->directory('jobs/telegram-qr')", $jobPostingForm);
        $this->assertStringContainsString("TextInput::make('telegramUrl')", $jobPostingForm);
        $this->assertStringContainsString("Select::make('telegramChannelId')", $jobPostingForm);
        $this->assertStringContainsString("'telegramQr'", $jobPostingModel);
        $this->assertStringContainsString("'telegramUrl'", $jobPostingModel);
        $this->assertStringContainsString("'telegramChannelId'", $jobPostingModel);
        $this->assertStringContainsString("Repeater::make('career_telegram_channels')", $settingsPage);
        $this->assertStringContainsString("'telegramQr' => \$jobDb->telegramQr", $careerController);
        $this->assertStringContainsString("'telegramUrl' => \$jobDb->telegramUrl", $careerController);
        $this->assertStringContainsString("'telegramChannelId' => \$jobDb->telegramChannelId", $careerController);
        $this->assertStringContainsString("SystemSetting::get('career_telegram_channels', [])", $careerController);
        $this->assertStringContainsString('$telegramQrUrl = PublicStorage::urlIfExists', $careerController);
        $this->assertStringContainsString("Str::startsWith(\$telegramUrl, ['https://', 'http://'])", $careerController);
        $this->assertStringContainsString('Carbon::JUST_NOW', $careerController);
        $this->assertStringContainsString("\$job['postedAt'] ?? \$job['postedDate'] ?? now()", $careerController);
        $this->assertStringContainsString('@if($telegramQrUrl)', $careerDetailsPage);
        $this->assertStringContainsString('w-32 h-32', $careerDetailsPage);
        $this->assertStringContainsString('href="{{ $telegramUrl }}"', $careerDetailsPage);
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
            'telegramUrl' => 'https://t.me/kimmexcareers',
        ]);

        $response = $this->get(route('careers.show', ['slug' => 'site-engineer']));

        $response
            ->assertOk()
            ->assertSee(__('Careers on Telegram'))
            ->assertSee('https://example.com/site-engineer-telegram-qr.png')
            ->assertSee('https://t.me/kimmexcareers');
    }

    public function test_career_details_use_the_selected_shared_telegram_channel_when_no_manual_override_exists(): void
    {
        SystemSetting::set('career_telegram_channels', [[
            'id' => '019fa987-4842-73e1-8dea-7b8ab0a40bc4',
            'name' => 'Kimmex Careers',
            'url' => 'https://t.me/kimmexcareers',
            'qr' => 'https://example.com/kimmex-careers-qr.png',
        ]]);

        JobPosting::create([
            'title' => ['en' => 'Project Engineer'],
            'slug' => 'project-engineer',
            'status' => JobPostingStatus::OPEN,
            'telegramChannelId' => '019fa987-4842-73e1-8dea-7b8ab0a40bc4',
        ]);

        $this->get(route('careers.show', ['slug' => 'project-engineer']))
            ->assertOk()
            ->assertSee('https://example.com/kimmex-careers-qr.png')
            ->assertSee('https://t.me/kimmexcareers');
    }
}
