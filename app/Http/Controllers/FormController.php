<?php

namespace App\Http\Controllers;

use App\Jobs\SendJobApplicationTelegramNotification;
use App\Mail\ContactAutoReplyMail;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\Subscriber;
use App\Models\SystemSetting;
use App\Rules\Turnstile;
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
        // 1. Anti-spam verification (Honeypot, Time-Trap, Content Analysis)
        if ($this->isSpamContactSubmission($request)) {
            // Fail silently with a generic success response so bots don't adapt
            return redirect()->back()->with('success', __('Thank you for your inquiry! We will get back to you shortly.'));
        }

        $validated = $request->validate([
            'cf-turnstile-response' => [config('services.turnstile.secret') ? 'required' : 'nullable', new Turnstile],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        // 2. Sanitize Inputs
        $sanitized = collect($validated)->except(['attachment', 'cf-turnstile-response'])->map(function ($value) {
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
            Log::info('Job application dropped: honeypot filled.', ['ip' => $request->ip()]);

            return redirect()->back()->with('success', __('Your application has been submitted successfully!'));
        }

        // 2. Time-trap check
        if ($request->has('_form_time')) {
            try {
                $formTime = (int) decrypt($request->input('_form_time'));
                $elapsed = time() - $formTime;
                if ($elapsed < 3 || $elapsed > 86400) {
                    Log::info('Job application dropped: time-trap triggered.', [
                        'ip' => $request->ip(),
                        'elapsed_seconds' => $elapsed,
                    ]);

                    return redirect()->back()->with('success', __('Your application has been submitted successfully!'));
                }
            } catch (\Throwable $e) {
                Log::info('Job application dropped: invalid form_time token.', ['ip' => $request->ip()]);

                return redirect()->back()->with('success', __('Your application has been submitted successfully!'));
            }
        }

        if (in_array($request->input('job_id'), ['gen', 'general-application'], true)) {
            $request->merge(['job_id' => null]);
        }

        $validated = $request->validate([
            'cf-turnstile-response' => [config('services.turnstile.secret') ? 'required' : 'nullable', new Turnstile],
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
        $sanitized = collect($validated)->except(['resume', 'cf-turnstile-response'])->map(function ($value) {
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

    /**
     * Determine if a contact inquiry submission is automated bot spam.
     */
    protected function isSpamContactSubmission(Request $request): bool
    {
        // 1. Honeypot check (hidden input that only bots fill)
        if ($request->filled('website_url')) {
            Log::info('Contact inquiry dropped: honeypot field filled.', [
                'ip' => $request->ip(),
            ]);

            return true;
        }

        // 2. Time-trap check (real humans take at least 3 seconds to fill out the form)
        if ($request->has('_form_time')) {
            try {
                $formTime = (int) decrypt($request->input('_form_time'));
                $elapsed = time() - $formTime;
                if ($elapsed < 3 || $elapsed > 86400) {
                    Log::info('Contact inquiry dropped: time-trap triggered.', [
                        'ip' => $request->ip(),
                        'elapsed_seconds' => $elapsed,
                    ]);

                    return true;
                }
            } catch (\Throwable $e) {
                Log::info('Contact inquiry dropped: invalid form_time token.', [
                    'ip' => $request->ip(),
                ]);

                return true;
            }
        }

        $firstName = trim((string) $request->input('first_name', ''));
        $lastName = trim((string) $request->input('last_name', ''));
        $subject = (string) $request->input('subject', '');
        $message = (string) $request->input('message', '');
        $combinedText = $subject.' '.$message;

        // 3. Known spam link shorteners or messaging redirection domains
        $spamLinkPatterns = [
            'telegra\.ph',
            't\.me',
            'wa\.me',
            'whatsapp\.com\/channel',
            'bit\.ly',
            'tinyurl\.com',
            'cutt\.ly',
            'is\.gd',
            'rb\.gy',
            'shorturl\.at',
        ];
        if (preg_match('/(?:https?:\/\/|www\.)[^\s]*(?:'.implode('|', $spamLinkPatterns).')/i', $combinedText)) {
            Log::info('Contact inquiry dropped: spam link detected.', [
                'ip' => $request->ip(),
                'subject' => $subject,
            ]);

            return true;
        }

        // 4. Multiple URLs in contact message (legitimate inquiries rarely have multiple external links)
        if (preg_match_all('/https?:\/\/|www\./i', $message) > 1) {
            Log::info('Contact inquiry dropped: multiple URLs in message.', [
                'ip' => $request->ip(),
            ]);

            return true;
        }

        // 5. Cyrillic text check (high-confidence indicator of spam bots on this site)
        if (preg_match('/[\p{Cyrillic}]/u', $combinedText)) {
            Log::info('Contact inquiry dropped: cyrillic script detected.', [
                'ip' => $request->ip(),
            ]);

            return true;
        }

        // 6. Common lottery / casino / crypto prize scam phrases
        $scamKeywords = [
            'aventador',
            'lamborghini',
            'win the prize',
            'you could win',
            'claim your prize',
            'prize you deserve',
            'crypto investment',
            'forex trading',
            'binance gift',
            'free spins',
            'online casino',
            'slot machine',
            'cialis',
            'viagra',
            'message-id',
        ];
        foreach ($scamKeywords as $keyword) {
            if (stripos($combinedText, $keyword) !== false) {
                Log::info('Contact inquiry dropped: scam keyword detected.', [
                    'ip' => $request->ip(),
                    'keyword' => $keyword,
                ]);

                return true;
            }
        }

        // 7. Bot name pattern (identical first and last name with mixed casing/numbers like HarryNetQE HarryNetQE)
        if (! empty($firstName) && strcasecmp($firstName, $lastName) === 0 && strlen($firstName) >= 8) {
            if (preg_match('/[A-Z].*[0-9]|[0-9].*[A-Z]/', $firstName)) {
                Log::info('Contact inquiry dropped: suspicious bot name pattern.', [
                    'ip' => $request->ip(),
                    'name' => $firstName,
                ]);

                return true;
            }
        }

        return false;
    }
}
