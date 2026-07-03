<?php

namespace Tests\Feature;

use App\Services\AIGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_openrouter_defaults_are_available_without_api_key(): void
    {
        $models = app(AIGeneratorService::class)->getAvailableModels(null, 'openrouter');

        $this->assertArrayHasKey('deepseek/deepseek-chat-v3-0324:free', $models);
    }

    public function test_openrouter_generates_content_with_chat_completion_api(): void
    {
        Http::fake([
            'openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Generated Kimmex content.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(AIGeneratorService::class)->generateContent(
            'sustainable construction',
            'article',
            null,
            [
                'provider' => 'openrouter',
                'openrouter' => [
                    'api_key' => 'or-test-key',
                    'model' => 'deepseek/deepseek-chat-v3-0324:free',
                ],
                'system_prompt' => 'You are a construction copywriter.',
                'temperature' => 0.4,
                'tone' => 'professional',
            ],
        );

        $this->assertSame('Generated Kimmex content.', $result);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://openrouter.ai/api/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer or-test-key')
                && $request['model'] === 'deepseek/deepseek-chat-v3-0324:free'
                && $request['temperature'] === 0.4
                && $request['messages'][0]['role'] === 'system'
                && $request['messages'][1]['role'] === 'user';
        });
    }

    public function test_openrouter_model_listing_marks_free_models(): void
    {
        Http::fake([
            'openrouter.ai/api/v1/models' => Http::response([
                'data' => [
                    [
                        'id' => 'provider/free-model:free',
                        'name' => 'Free Model',
                        'architecture' => ['input_modalities' => ['text']],
                        'pricing' => ['prompt' => '0', 'completion' => '0'],
                    ],
                    [
                        'id' => 'provider/paid-model',
                        'name' => 'Paid Model',
                        'architecture' => ['input_modalities' => ['text']],
                        'pricing' => ['prompt' => '0.000001', 'completion' => '0.000002'],
                    ],
                ],
            ], 200),
        ]);

        $models = app(AIGeneratorService::class)->getAvailableModels('or-test-key', 'openrouter');

        $this->assertSame('Free Model (Free)', $models['provider/free-model:free']);
        $this->assertSame('Paid Model', $models['provider/paid-model']);
    }
}
