<?php

namespace App\Http\Controllers;

use App\Jobs\SendJobApplicationTelegramNotification;
use App\Mail\ContactAutoReplyMail;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\Subscriber;
use App\Models\SystemSetting;
use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class FormController extends Controller
{
    public function showContact(): View
    {
        $profile = SystemSetting::get('organization_profile', []);
        $lang = app()->getLocale();
        $email = $profile['email'] ?? 'info@kimmex.com.kh';
        $phone = $profile['phone'] ?? '+855 23 999 999';
        $address = $profile[$lang]['address'] ?? ($profile['en']['address'] ?? __('Phnom Penh, Cambodia'));
        $googleMapsUrl = $profile['google_maps_url'] ?? '';
        $originalMapsUrl = $googleMapsUrl;

        $defaultEmbed = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.667785689154!2d104.89350269999998!3d11.575656499999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31095176fe4b5e51%3A0x844dbeef5ee9d25b!2sKim%20mex%20Construction%20%26%20Investment%20Co.%2Cltd!5e0!3m2!1skm!2skh!4v1775701743611!5m2!1skm!2skh';

        $isEmbed = str_contains($googleMapsUrl, '/maps/embed') || str_contains($googleMapsUrl, 'google.com/maps?pb=') || str_contains($googleMapsUrl, 'output=embed');

        if (! $isEmbed && ! empty($googleMapsUrl)) {
            $googleMapsUrl = $defaultEmbed;
        } elseif (empty($googleMapsUrl)) {
            $googleMapsUrl = $defaultEmbed;
        }

        $googleMapsLink = ! empty($originalMapsUrl) && ! $isEmbed ? $originalMapsUrl : 'https://www.google.com/maps/search/?api=1&query='.urlencode($address);

        $facebook = $profile['facebook'] ?? '#';
        $linkedin = $profile['linkedin'] ?? '#';
        $youtube = $profile['youtube'] ?? '#';
        $instagram = $profile['instagram'] ?? '#';
        $telegram = $profile['telegram'] ?? '#';
        $tiktok = $profile['tiktok'] ?? '#';
        $workingHours = $profile[$lang]['working_hours'] ?? ($profile['en']['working_hours'] ?? 'Mon - Fri: 8:00 AM - 5:00 PM');

        return view('pages.contact', compact(
            'email',
            'phone',
            'address',
            'googleMapsUrl',
            'googleMapsLink',
            'facebook',
            'linkedin',
            'youtube',
            'instagram',
            'telegram',
            'tiktok',
            'workingHours'
        ));
    }

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

    public function unsubscribe(string $token): View
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if (! $subscriber) {
            abort(404);
        }

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return view('pages.unsubscribed', ['email' => $subscriber->email]);
    }
}
