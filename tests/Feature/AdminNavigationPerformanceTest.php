<?php

namespace Tests\Feature;

use App\Livewire\AiSwitcher;
use App\Models\SystemSetting;
use App\Services\AIGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class AdminNavigationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_navigation_does_not_fetch_ai_models_when_the_switcher_mounts(): void
    {
        SystemSetting::set('ai_settings', [
            'provider' => 'gemini',
            'gemini' => ['api_key' => 'test-key', 'model' => 'gemini-3.1-flash-lite'],
        ]);

        Http::fake();

        Livewire::test(AiSwitcher::class)
            ->assertSet('availableModels', [
                'gemini-3.1-flash-lite' => 'gemini-3.1-flash-lite',
            ]);

        Http::assertNothingSent();
    }

    public function test_available_ai_models_are_cached_after_an_administrator_requests_them(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [[
                    'name' => 'models/gemini-test',
                    'displayName' => 'Gemini Test',
                    'supportedGenerationMethods' => ['generateContent'],
                ]],
            ]),
        ]);

        $service = new AIGeneratorService;

        $this->assertSame(
            ['models/gemini-test' => 'Gemini Test'],
            $service->getAvailableModels('test-key', 'gemini'),
        );
        $this->assertSame(
            ['models/gemini-test' => 'Gemini Test'],
            $service->getAvailableModels('test-key', 'gemini'),
        );

        Http::assertSentCount(1);
    }
}
