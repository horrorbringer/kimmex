<?php

namespace App\Jobs;

use App\Models\Inquiry;
use App\Services\TelegramService;
use App\Support\PublicStorage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SendInquiryTelegramNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 25;

    /** @var array<int> */
    public array $backoff = [10, 60];

    public function __construct(public Inquiry $inquiry) {}

    public function handle(TelegramService $telegram): void
    {
        $sent = $telegram->notifyInquiry([
            'name' => $this->inquiry->name,
            'email' => $this->inquiry->email,
            'subject' => $this->inquiry->subject,
            'message' => $this->inquiry->message,
            'ip_address' => $this->inquiry->ip_address,
            'file_disk' => PublicStorage::diskName(),
            'file_path' => $this->inquiry->attachment_url,
        ]);

        if (! $sent) {
            throw new RuntimeException('Telegram inquiry notification was not sent.');
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Telegram inquiry notification failed after retries.', [
            'inquiry_id' => $this->inquiry->id,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
