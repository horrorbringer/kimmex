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
        } else {
            Subscriber::create([
                'email' => $this->email,
            ]);
        }

        $this->subscribed = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.subscribe-form');
    }
}
