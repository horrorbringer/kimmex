<?php

namespace App\Observers;

use App\Jobs\SendInquiryTelegramNotification;
use App\Models\Inquiry;

class InquiryObserver
{
    public function created(Inquiry $inquiry): void
    {
        SendInquiryTelegramNotification::dispatch($inquiry)->afterCommit();
    }
}
