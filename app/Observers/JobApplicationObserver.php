<?php

namespace App\Observers;

use App\Enums\ApplicationStatus;
use App\Mail\ApplicationStatusMail;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class JobApplicationObserver
{
    public function updated(JobApplication $application): void
    {
        if (!$application->wasChanged('status')) {
            return;
        }

        $status = $application->status;

        // Only send email if status is a valid enum (handle legacy string values)
        if (!$status instanceof ApplicationStatus) {
            $status = ApplicationStatus::tryFrom($application->getRawOriginal('status'));
            if (!$status) {
                return;
            }
        }

        if (!$application->email) {
            return;
        }

        try {
            Mail::to($application->email)
                ->queue(new ApplicationStatusMail($application, $status));
        } catch (\Throwable $e) {
            Log::error('Failed to send application status email: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'status' => $status->value,
            ]);
        }
    }
}
