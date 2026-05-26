<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SystemSetting;
use Exception;

class AIGeneratorService
{
    public function generateContent(string $topic, string $type = 'article', ?string $customInstructions = null, ?array $overrideSettings = null): ?string
    {
        $settings = $overrideSettings ?: SystemSetting::get('ai_settings', []);
        $provider = $settings['provider'] ?? 'gemini';
        $providerConfig = $this->providerConfig($settings, $provider);
        $apiKey = $providerConfig['api_key'];
        $model = $providerConfig['model'];
        $baseUrl = $providerConfig['base_url'];
        $systemPrompt = $settings['system_prompt'] ?? 'You are an expert copywriter.';
        $temperature = (float) ($settings['temperature'] ?? 0.7);
        $tone = $settings['tone'] ?? 'professional';

        if ($provider !== 'ollama' && empty($apiKey)) {
            throw new Exception("AI API Key is not configured.");
        }

        $systemPrompt .= " Tone: {$tone}.";

        try {
            $result = match ($provider) {
                'gemini' => $this->generateWithGemini($topic, $type, $customInstructions, $apiKey, $model, $systemPrompt, $temperature),
                'ollama' => $this->generateWithOllama($topic, $type, $customInstructions, $baseUrl, $model, $systemPrompt, $temperature),
                default => throw new Exception("Unsupported AI Provider: " . $provider),
            };
            $this->trackUsage($provider, true);
            return $result;
        } catch (Exception $e) {
            $this->trackUsage($provider, false, $e->getMessage());
            throw $e;
        }
    }

    protected function trackUsage(string $provider, bool $success, ?string $error = null): void
    {
        $stats = SystemSetting::get('ai_stats', [
            'today_count' => 0,
            'last_reset' => now()->toDateString(),
            'last_status' => 'unknown',
            'last_error' => null,
            'total_count' => 0
        ]);

        if (($stats['last_reset'] ?? '') !== now()->toDateString()) {
            $stats['today_count'] = 0;
            $stats['last_reset'] = now()->toDateString();
        }

        if ($success) {
            $stats['today_count']++;
            $stats['total_count']++;
            $stats['last_status'] = 'healthy';
            $stats['last_error'] = null;
        } else {
            $stats['last_status'] = 'error';
            $stats['last_error'] = $error;
        }

        SystemSetting::set('ai_stats', $stats);
    }

    public function improveContent(string $content, ?string $instructions = null, ?array $overrideSettings = null): ?string
    {
        $settings = $overrideSettings ?: SystemSetting::get('ai_settings', []);
        $provider = $settings['provider'] ?? 'gemini';
        $providerConfig = $this->providerConfig($settings, $provider);
        $apiKey = $providerConfig['api_key'];
        $model = $providerConfig['model'];
        $baseUrl = $providerConfig['base_url'];
        $systemPrompt = $settings['system_prompt'] ?? 'You are an expert copywriter.';
        $temperature = (float) ($settings['temperature'] ?? 0.7);
        $tone = $settings['tone'] ?? 'professional';

        if ($provider !== 'ollama' && empty($apiKey)) {
            throw new Exception("AI API Key is not configured.");
        }

        $systemPrompt .= " Tone: {$tone}.";

        try {
            $result = match ($provider) {
                'gemini' => $this->improveWithGemini($content, $instructions, $apiKey, $model, $systemPrompt, $temperature),
                'ollama' => $this->improveWithOllama($content, $instructions, $baseUrl, $model, $systemPrompt, $temperature),
                default => throw new Exception("Unsupported AI Provider: " . $provider),
            };
            $this->trackUsage($provider, true);
            return $result;
        } catch (Exception $e) {
            $this->trackUsage($provider, false, $e->getMessage());
            throw $e;
        }
    }

    protected function generateWithGemini(string $topic, string $type, ?string $customInstructions, string $apiKey, string $model, string $systemPrompt, float $temperature): ?string
    {
        $modelPath = str_starts_with($model, 'models/') ? $model : "models/{$model}";
        $url = "https://generativelanguage.googleapis.com/v1beta/{$modelPath}:generateContent?key={$apiKey}";
        
        $prompt = "Task: Write a {$type} about '{$topic}'.\n\nSystem Guidelines: {$systemPrompt}\n";
        if ($customInstructions) {
            $prompt .= "\nSpecific User Instructions: {$customInstructions}\n";
        }
        $prompt .= "\nFormat: Provide formatted text (HTML allowed if necessary) but ONLY return the content. Do not include markdown code blocks.";

        return $this->callGemini($url, $prompt, $temperature);
    }

    protected function improveWithGemini(string $content, ?string $instructions, string $apiKey, string $model, string $systemPrompt, float $temperature): ?string
    {
        $modelPath = str_starts_with($model, 'models/') ? $model : "models/{$model}";
        $url = "https://generativelanguage.googleapis.com/v1beta/{$modelPath}:generateContent?key={$apiKey}";

        $prompt = "Task: Improve the following content to be more professional, engaging, and clear.\n\n";
        $prompt .= "Original Content:\n{$content}\n\n";
        $prompt .= "System Guidelines: {$systemPrompt}\n";
        if ($instructions) {
            $prompt .= "\nSpecific User Instructions: {$instructions}\n";
        }
        $prompt .= "\nFormat: ONLY return the improved text. Do not explain what you changed. Keep the same HTML structure if present.";

        return $this->callGemini($url, $prompt, $temperature);
    }

    protected function generateWithOllama(string $topic, string $type, ?string $customInstructions, string $baseUrl, string $model, string $systemPrompt, float $temperature): ?string
    {
        $url = rtrim($baseUrl, '/') . '/api/generate';
        
        $prompt = "System: {$systemPrompt}\nTask: Write a {$type} about '{$topic}'.";
        if ($customInstructions) $prompt .= "\nInstructions: {$customInstructions}";
        $prompt .= "\n\nReturn ONLY the content. No preamble.";

        return $this->callOllama($url, $prompt, $model, $temperature);
    }

    protected function improveWithOllama(string $content, ?string $instructions, string $baseUrl, string $model, string $systemPrompt, float $temperature): ?string
    {
        $url = rtrim($baseUrl, '/') . '/api/generate';

        $prompt = "System: {$systemPrompt}\nTask: Improve the content below.\nContent: {$content}";
        if ($instructions) $prompt .= "\nInstructions: {$instructions}";
        $prompt .= "\n\nReturn ONLY the improved text.";

        return $this->callOllama($url, $prompt, $model, $temperature);
    }

    protected function callGemini(string $url, string $prompt, float $temperature): ?string
    {
        $response = Http::post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
            ]
        ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text');
            $text = preg_replace('/^```(?:html)?\n/s', '', $text);
            $text = preg_replace('/\n```$/s', '', $text);
            
            return trim($text);
        }

        $error = $response->json('error.message') ?? $response->body();
        throw new Exception("Gemini Error: " . $error);
    }

    protected function callOllama(string $url, string $prompt, string $model, float $temperature): ?string
    {
        $response = Http::post($url, [
            'model' => $model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => $temperature,
            ]
        ]);

        if ($response->successful()) {
            return trim($response->json('response'));
        }

        throw new Exception("Ollama Error: " . $response->body());
    }

    public function getAvailableModels(?string $apiKey = null, ?string $provider = 'gemini', ?string $baseUrl = null): array
    {
        $settings = SystemSetting::get('ai_settings', []);
        $providerConfig = $this->providerConfig($settings, $provider ?? 'gemini');
        $apiKey = $apiKey ?: $providerConfig['api_key'];
        $baseUrl = $baseUrl ?: $providerConfig['base_url'];
        
        try {
            if ($provider === 'gemini') {
                if (empty($apiKey)) return [];
                $response = Http::get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
                if ($response->successful()) {
                    return collect($response->json()['models'] ?? [])
                        ->filter(fn($m) => in_array('generateContent', $m['supportedGenerationMethods'] ?? []))
                        ->mapWithKeys(fn($m) => [$m['name'] => $m['displayName']])
                        ->toArray();
                }
            }

            if ($provider === 'ollama') {
                $response = Http::get(rtrim($baseUrl, '/') . '/api/tags');
                if ($response->successful()) {
                    return collect($response->json()['models'] ?? [])
                        ->mapWithKeys(fn($m) => [$m['name'] => $m['name']])
                        ->toArray();
                }
            }
        } catch (Exception $e) {
            return [];
        }

        return [
            'models/gemini-1.5-flash' => 'Gemini 1.5 Flash (Default)',
            'models/gemini-1.5-pro' => 'Gemini 1.5 Pro',
        ];
    }

    protected function providerConfig(array $settings, string $provider): array
    {
        $gemini = $settings['gemini'] ?? [];
        $ollama = $settings['ollama'] ?? [];

        return [
            'api_key' => $provider === 'gemini'
                ? ($gemini['api_key'] ?? $settings['api_key'] ?? '')
                : ($settings['api_key'] ?? ''),
            'model' => $provider === 'ollama'
                ? ($ollama['model'] ?? $settings['model'] ?? 'llama3.1')
                : ($gemini['model'] ?? $settings['model'] ?? 'gemini-1.5-flash'),
            'base_url' => $provider === 'ollama'
                ? ($ollama['base_url'] ?? $settings['base_url'] ?? 'http://localhost:11434')
                : ($settings['base_url'] ?? 'http://localhost:11434'),
        ];
    }
}
