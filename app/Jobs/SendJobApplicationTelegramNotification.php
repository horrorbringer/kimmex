<?php

namespace App\Jobs;

use App\Models\JobApplication;
use App\Services\TelegramService;
use App\Support\PublicStorage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SendJobApplicationTelegramNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 25;

    /** @var array<int> */
    public array $backoff = [10, 60];

    public function __construct(public JobApplication $application) {}

    public function handle(TelegramService $telegram): void
    {
        $sent = $telegram->notifyJobApplication([
            'name' => $this->application->applicantName,
            'email' => $this->application->email,
            'phone' => $this->application->phone,
            'position' => $this->application->job?->title ?? 'General Application',
            'file_disk' => PublicStorage::diskName(),
            'file_path' => $this->application->resumeUrl,
        ]);

        if (! $sent) {
            throw new RuntimeException('Telegram job application notification was not sent.');
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Telegram job application notification failed after retries.', [
            'application_id' => $this->application->id,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
