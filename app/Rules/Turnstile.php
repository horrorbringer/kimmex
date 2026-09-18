<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class Turnstile implements ValidationRule
{
    public bool $implicit = true;

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.turnstile.secret');

        // If turnstile secret is not configured, allow pass-through (e.g. in local development without keys)
        if (blank($secret)) {
            return;
        }

        // If secret is configured but token is empty
        if (blank($value)) {
            $fail(__('Security verification is required. Please try again.'));

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (! $response->successful() || ! $response->json('success')) {
                Log::warning('Turnstile verification failed.', [
                    'ip' => request()->ip(),
                    'error_codes' => $response->json('error-codes', []),
                ]);

                $fail(__('Security verification failed. Please refresh the page and try again.'));
            }
        } catch (\Throwable $e) {
            Log::error('Turnstile connection error: '.$e->getMessage());

            $fail(__('Security check could not be completed. Please try again shortly.'));
        }
    }
}
