<?php

namespace App\Observers;

use App\Jobs\SendInquiryTelegramNotification;
use App\Models\Inquiry;

class InquiryObserver
{
    public function created(Inquiry $inquiry): void
    {
        if ($inquiry->status === 'SPAM') {
            return;
        }

        SendInquiryTelegramNotification::dispatch($inquiry)->afterCommit();
    }
}
