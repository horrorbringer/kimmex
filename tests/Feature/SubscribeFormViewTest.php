<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SubscribeFormViewTest extends TestCase
{
    public function test_footer_subscription_form_hides_optional_interest_choices(): void
    {
        $template = File::get(resource_path('views/livewire/subscribe-form.blade.php'));

        $this->assertStringNotContainsString("__('Interests (optional)')", $template);
        $this->assertStringNotContainsString('type="checkbox"', $template);
        $this->assertStringNotContainsString('wire:model="interests"', $template);
    }
}
