<?php

namespace App\Observers;

use App\Models\Inquiry;
use App\Services\TelegramService;
use App\Support\PublicStorage;
use Illuminate\Support\Facades\Log;

class InquiryObserver
{
    public function created(Inquiry $inquiry): void
    {
        try {
            $telegram = app(TelegramService::class);

            $telegram->notifyInquiry([
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'subject' => $inquiry->subject,
                'message' => $inquiry->message,
                'ip_address' => $inquiry->ip_address,
                'file_disk' => PublicStorage::diskName(),
                'file_path' => $inquiry->attachment_url,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send Telegram inquiry notification: '.$e->getMessage(), [
                'inquiry_id' => $inquiry->id,
            ]);
        }
    }
}
