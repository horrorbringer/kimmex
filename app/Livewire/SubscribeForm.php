<?php

namespace App\Livewire;

use App\Models\Subscriber;
use Livewire\Component;

class SubscribeForm extends Component
{
    public string $email = '';
    public bool $subscribed = false;
    public string $error = '';

    public function subscribe(): void
    {
        $this->error = '';

        // Rate limit: max 3 subscribe attempts per minute per IP
        $key = 'subscribe_' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $this->error = __('Too many attempts. Please try again later.');
            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        $this->validate([
            'email' => 'required|email|max:255',
        ]);

        // Check if already subscribed
        $existing = Subscriber::where('email', $this->email)->first();

        if ($existing) {
            if ($existing->is_active) {
                $this->error = __('This email is already subscribed.');
                return;
            }
            // Reactivate
            $existing->update(['is_active' => true, 'unsubscribed_at' => null, 'subscribed_at' => now()]);
            $subscriber = $existing;
        } else {
            try {
                $subscriber = Subscriber::create([
                    'email' => $this->email,
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                $this->error = __('This email is already subscribed.');
                return;
            }
        }

        // Send welcome email
        try {
            \Illuminate\Support\Facades\Mail::to($subscriber->email)
                ->send(new \App\Mail\WelcomeSubscriberMail($subscriber));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Welcome email failed', ['email' => $subscriber->email, 'error' => $e->getMessage()]);
        }

        $this->subscribed = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.subscribe-form');
    }
}
