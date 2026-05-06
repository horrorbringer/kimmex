<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SystemSetting;
use Exception;

class AIGeneratorService
{
    public function generateContent(string $topic, string $type = 'article', ?string $customInstructions = null): ?string
    {
        $settings = SystemSetting::get('ai_settings', []);
        $provider = $settings['provider'] ?? 'gemini';
        $apiKey = $settings['api_key'] ?? '';
        $model = $settings['model'] ?? 'gemini-1.5-flash';
        $systemPrompt = $settings['system_prompt'] ?? 'You are an expert copywriter. Return only the content without any pleasantries.';

        if (empty($apiKey)) {
            throw new Exception("AI API Key is not configured in settings. Please go to Settings > AI Generator Settings.");
        }

        if ($provider === 'gemini') {
            return $this->generateWithGemini($topic, $type, $customInstructions, $apiKey, $model, $systemPrompt);
        }

        // Add more providers here in the future if needed (e.g., OpenAI)

        throw new Exception("Unsupported AI Provider: " . $provider);
    }

    protected function generateWithGemini(string $topic, string $type, ?string $customInstructions, string $apiKey, string $model, string $systemPrompt): ?string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        
        $prompt = "Task: Write a {$type} about '{$topic}'.\n\nSystem Guidelines: {$systemPrompt}\n";
        if ($customInstructions) {
            $prompt .= "\nSpecific User Instructions: {$customInstructions}\n";
        }
        $prompt .= "\nFormat: Provide formatted text (HTML allowed if necessary) but ONLY return the content. Do not say 'Here is the article' or include markdown code blocks like ```html.";

        $response = Http::post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text');
            // Clean up Markdown code blocks if Gemini accidentally wraps it
            $text = preg_replace('/^```(?:html)?\n/s', '', $text);
            $text = preg_replace('/\n```$/s', '', $text);
            
            return trim($text);
        }

        $error = $response->json('error.message') ?? $response->body();
        throw new Exception("AI Generation failed: " . $error);
    }
}
