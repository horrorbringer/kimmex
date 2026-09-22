<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderAdminButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_button_is_hidden_for_guests(): void
    {
        $rendered = view('components.header')->render();

        $this->assertStringNotContainsString('href="/admin"', $rendered);
    }

    public function test_admin_button_is_hidden_for_non_admin_users(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $rendered = view('components.header')->render();

        $this->assertStringNotContainsString('href="/admin"', $rendered);
    }

    public function test_admin_button_renders_with_new_tab_and_icon_for_admins(): void
    {
        $admin = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        $this->actingAs($admin);

        $rendered = view('components.header')->render();

        // Must link to /admin in new tab with security attributes
        $this->assertStringContainsString('href="/admin"', $rendered);
        $this->assertStringContainsString('target="_blank"', $rendered);
        $this->assertStringContainsString('rel="noopener noreferrer"', $rendered);

        // Must display ADMIN text and new-tab indicator
        $this->assertStringContainsString('ADMIN', $rendered);
        $this->assertStringContainsString('title="Open Admin Panel in new tab"', $rendered);
        $this->assertStringContainsString('<svg', $rendered);
    }
}
