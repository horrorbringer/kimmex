<?php

namespace App\Http\Controllers;

use App\Jobs\SendJobApplicationTelegramNotification;
use App\Mail\ContactAutoReplyMail;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            'name' => $sanitized['first_name'].' '.$sanitized['last_name'],
            'email' => $sanitized['email'],
            'phone' => $sanitized['phone'],
            'subject' => $sanitized['subject'] ?? 'Website Inquiry',
            'message' => $sanitized['message'],
            'attachment_url' => $attachmentPath,
            'ip_address' => $request->ip(),
            'status' => 'NEW',
        ]);

        // 3. Auto-reply email to the user. The inquiry observer sends the Telegram alert.
        try {
            Mail::to($inquiry->email)
                ->queue((new ContactAutoReplyMail($inquiry))->afterCommit());
        } catch (\Exception $e) {
            Log::error('Contact auto-reply email error: '.$e->getMessage());
        }

        return redirect()->back()->with('success', __('Thank you for your inquiry! We will get back to you shortly.'));
    }

    public function submitApplication(Request $request)
    {
        // 1. Honeypot check
        if ($request->filled('website_url')) {
            return redirect()->back(); // Fail silently for bots
        }

        if (in_array($request->input('job_id'), ['gen', 'general-application'], true)) {
            $request->merge(['job_id' => null]);
        }

        $validated = $request->validate([
            'job_id' => 'present|nullable|uuid|exists:job_postings,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+?[\d\s\-().]{7,25}$/'],
            'resume' => 'required|file|mimes:pdf,doc,docx|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
            'message' => 'nullable|string',
        ], [
            'phone.regex' => __('Please enter a valid phone number (e.g. +855 12 345 678).'),
            'phone.max' => __('Phone number is too long.'),
        ]);

        // 2. Sanitize Inputs
        $sanitized = collect($validated)->except('resume')->map(function ($value) {
            return is_string($value) ? strip_tags($value) : $value;
        })->all();

        $resumePath = $request->file('resume')->store('resumes', PublicStorage::diskName());

        $application = JobApplication::create([
            'jobId' => $sanitized['job_id'] ?? null,
            'applicantName' => $sanitized['full_name'],
            'email' => $sanitized['email'],
            'phone' => $sanitized['phone'],
            'resumeUrl' => $resumePath,
            'coverLetter' => $sanitized['message'] ?? '',
            'status' => 'PENDING',
            'submittedAt' => now(),
        ]);

        // Telegram fetches the resume from Cloudinary and sends it to Telegram. Do this
        // in the queue so the visitor is not kept waiting for two remote file transfers.
        SendJobApplicationTelegramNotification::dispatch($application)->afterCommit();

        return redirect()->back()->with('success', __('Your application has been submitted successfully!'));
    }
}
