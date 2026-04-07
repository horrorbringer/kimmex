<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\JobApplication;

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
        ]);

        // 2. Sanitize Inputs
        $sanitized = collect($validated)->map(function ($value) {
            return is_string($value) ? strip_tags($value) : $value;
        })->all();

        Inquiry::create([
            'name' => $sanitized['first_name'] . ' ' . $sanitized['last_name'],
            'email' => $sanitized['email'],
            'phone' => $sanitized['phone'],
            'subject' => $sanitized['subject'] ?? 'Website Inquiry',
            'message' => $sanitized['message'],
            'status' => 'NEW',
        ]);

        return redirect()->back()->with('success', __('Thank you for your inquiry! We will get back to you shortly.'));
    }

    public function submitApplication(Request $request)
    {
        // 1. Honeypot check
        if ($request->filled('website_url')) {
            return redirect()->back(); // Fail silently for bots
        }

        $validated = $request->validate([
            'job_id' => 'required|string|max:255', // Simplified from job_posting_id
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'resume' => 'required|file|mimes:pdf,doc,docx|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240', // 10MB Max + Mimetype check
            'message' => 'nullable|string', // Renamed from cover_letter to match view
        ]);

        // 2. Sanitize Inputs
        $sanitized = collect($validated)->except('resume')->map(function ($value) {
            return is_string($value) ? strip_tags($value) : $value;
        })->all();

        $resumePath = $request->file('resume')->store('resumes', 'public');

        JobApplication::create([
            'jobId' => $sanitized['job_id'] === 'gen' ? null : $sanitized['job_id'],
            'applicantName' => $sanitized['full_name'],
            'email' => $sanitized['email'],
            'phone' => $sanitized['phone'],
            'resumeUrl' => $resumePath,
            'coverLetter' => $sanitized['message'] ?? '',
            'status' => 'PENDING',
            'submittedAt' => now(),
        ]);

        return redirect()->back()->with('success', __('Your application has been submitted successfully!'));
    }
}
