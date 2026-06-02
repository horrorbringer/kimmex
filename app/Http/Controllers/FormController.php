<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Support\PublicStorage;

class FormController extends Controller
{
    public function submitContact(Request $request)
    {
        // 1. Honeypot check
        if ($request->filled('website_url')) {
            return redirect()->back(); // Fail silently for bots
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        // 2. Sanitize Inputs
        $sanitized = collect($validated)->except('attachment')->map(function ($value) {
            return is_string($value) ? strip_tags($value) : $value;
        })->all();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', PublicStorage::diskName());
        }

        $inquiry = Inquiry::create([
            'name' => $sanitized['first_name'] . ' ' . $sanitized['last_name'],
            'email' => $sanitized['email'],
            'phone' => $sanitized['phone'],
            'subject' => $sanitized['subject'] ?? 'Website Inquiry',
            'message' => $sanitized['message'],
            'attachment_url' => $attachmentPath,
            'status' => 'NEW',
        ]);

        // 3. Smart Telegram Notification (Departmental Routing)
        try {
            $telegram = new \App\Services\TelegramService();
            $telegram->notifyInquiry([
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'subject' => $inquiry->subject,
                'message' => $inquiry->message,
                'file_disk' => PublicStorage::diskName(),
                'file_path' => $attachmentPath,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Telegram notification error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', __('Thank you for your inquiry! We will get back to you shortly.'));
    }

    public function submitApplication(Request $request)
    {
        // 1. Honeypot check
        if ($request->filled('website_url')) {
            return redirect()->back(); // Fail silently for bots
        }

        $validated = $request->validate([
            'job_id' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'resume' => 'required|file|mimes:pdf,doc,docx|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
            'message' => 'nullable|string',
        ]);

        // 2. Sanitize Inputs
        $sanitized = collect($validated)->except('resume')->map(function ($value) {
            return is_string($value) ? strip_tags($value) : $value;
        })->all();

        $resumePath = $request->file('resume')->store('resumes', PublicStorage::diskName());

        $application = JobApplication::create([
            'jobId' => $sanitized['job_id'] === 'gen' ? null : $sanitized['job_id'],
            'applicantName' => $sanitized['full_name'],
            'email' => $sanitized['email'],
            'phone' => $sanitized['phone'],
            'resumeUrl' => $resumePath,
            'coverLetter' => $sanitized['message'] ?? '',
            'status' => 'PENDING',
            'submittedAt' => now(),
        ]);

        // 3. Smart Telegram Notification (Departmental Routing)
        try {
            $jobTitle = 'General Application';
            if ($application->jobId) {
                $job = \App\Models\JobPosting::find($application->jobId);
                if ($job) {
                    $jobTitle = $job->title; // Automatically handles current locale
                }
            }

            $telegram = new \App\Services\TelegramService();
            $telegram->notifyJobApplication([
                'name' => $application->applicantName,
                'email' => $application->email,
                'phone' => $application->phone,
                'position' => $jobTitle,
                'file_disk' => PublicStorage::diskName(),
                'file_path' => $resumePath,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Telegram notification error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', __('Your application has been submitted successfully!'));
    }
}
