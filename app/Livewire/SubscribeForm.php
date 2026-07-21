<?php

namespace App\Livewire;

use App\Mail\WelcomeSubscriberMail;
use App\Models\Subscriber;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class SubscribeForm extends Component
{
    public string $email = '';

    public array $interests = [];

    public bool $subscribed = false;

    public string $error = '';

    public function getAvailableInterests(): array
    {
        return Subscriber::AVAILABLE_TAGS;
    }

    public function subscribe(): void
    {
        $this->error = '';

        // Rate limit: max 3 subscribe attempts per minute per IP
        $key = 'subscribe_'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->error = __('Too many attempts. Please try again later.');

            return;
        }
        RateLimiter::hit($key, 60);

        $this->validate([
            'email' => 'required|email|max:255',
            'interests' => 'array',
            'interests.*' => 'string|in:'.implode(',', array_keys(Subscriber::AVAILABLE_TAGS)),
        ]);

        // Default to 'general' if no interests selected
        $tags = ! empty($this->interests) ? $this->interests : ['general'];

        // Check if already subscribed
        $existing = Subscriber::where('email', $this->email)->first();

        if ($existing) {
            if ($existing->is_active) {
                $this->error = __('This email is already subscribed.');

                return;
            }
            // Reactivate
            $existing->update(['is_active' => true, 'unsubscribed_at' => null, 'subscribed_at' => now(), 'tags' => $tags]);
            $subscriber = $existing;
        } else {
            try {
                $subscriber = Subscriber::create([
                    'email' => $this->email,
                    'tags' => $tags,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                $this->error = __('This email is already subscribed.');

                return;
            }
        }

        // Send welcome email
        try {
            Mail::to($subscriber->email)
                ->send(new WelcomeSubscriberMail($subscriber));
        } catch (\Throwable $e) {
            Log::warning('Welcome email failed', ['email' => $subscriber->email, 'error' => $e->getMessage()]);
        }

        $this->subscribed = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.subscribe-form');
    }
}
