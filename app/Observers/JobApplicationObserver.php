<?php

namespace App\Observers;

use App\Enums\ApplicationStatus;
use App\Enums\JobPostingStatus;
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

        // When an application is ACCEPTED: auto-fill the job and auto-reject others
        if ($status === ApplicationStatus::ACCEPTED) {
            $this->autoFillJobAndRejectOthers($application);
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

    /**
     * When an applicant is accepted:
     * 1. Mark the job posting as FILLED
     * 2. Auto-reject all other non-final applicants for the same job
     */
    private function autoFillJobAndRejectOthers(JobApplication $acceptedApplication): void
    {
        // Mark job as FILLED
        $job = $acceptedApplication->job;
        if ($job && $job->status !== JobPostingStatus::FILLED) {
            $job->update(['status' => JobPostingStatus::FILLED]);
        }

        // Auto-reject other applicants who are still in progress
        $otherApplications = JobApplication::where('jobId', $acceptedApplication->jobId)
            ->where('id', '!=', $acceptedApplication->id)
            ->whereNotIn('status', [
                ApplicationStatus::ACCEPTED->value,
                ApplicationStatus::REJECTED->value,
            ])
            ->get();

        foreach ($otherApplications as $otherApp) {
            $otherApp->update(['status' => ApplicationStatus::REJECTED]);
            // The email will be triggered by this observer recursively
        }
    }
}
