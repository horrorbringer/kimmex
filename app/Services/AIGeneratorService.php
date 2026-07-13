<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SystemSetting;
use Exception;

class AIGeneratorService
{
    public function generateContent(string $topic, ?string $type = 'article', ?string $customInstructions = null, ?array $overrideSettings = null): ?string
    {
        $type = $type ?? 'article';
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
                'openrouter' => $this->generateWithOpenRouter($topic, $type, $customInstructions, $apiKey, $model, $systemPrompt, $temperature),
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
                'openrouter' => $this->improveWithOpenRouter($content, $instructions, $apiKey, $model, $systemPrompt, $temperature),
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

    public function translateContent(string $content, string $targetLanguage = 'Khmer', ?array $overrideSettings = null): ?string
    {
        $settings = $overrideSettings ?: SystemSetting::get('ai_settings', []);
        $provider = $settings['provider'] ?? 'gemini';
        $providerConfig = $this->providerConfig($settings, $provider);
        $apiKey = $providerConfig['api_key'];
        $model = $providerConfig['model'];
        $baseUrl = $providerConfig['base_url'];
        $temperature = 0.2;

        if ($provider !== 'ollama' && empty($apiKey)) {
            throw new Exception("AI API Key is not configured.");
        }

        try {
            $result = match ($provider) {
                'gemini' => $this->translateWithGemini($content, $targetLanguage, $apiKey, $model, $temperature),
                'openrouter' => $this->translateWithOpenRouter($content, $targetLanguage, $apiKey, $model, $temperature),
                'ollama' => $this->translateWithOllama($content, $targetLanguage, $baseUrl, $model, $temperature),
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

    protected function translateWithGemini(string $content, string $targetLanguage, string $apiKey, string $model, float $temperature): ?string
    {
        $modelPath = str_starts_with($model, 'models/') ? $model : "models/{$model}";
        $url = "https://generativelanguage.googleapis.com/v1beta/{$modelPath}:generateContent?key={$apiKey}";

        $prompt = "Task: Translate the following content into {$targetLanguage}.\n\n";
        $prompt .= "Content:\n{$content}\n\n";
        $prompt .= "Format: ONLY return the translated text. Preserve names, construction terms, numbers, punctuation, and any HTML tags. Do not explain the translation.";

        return $this->callGemini($url, $prompt, $temperature);
    }

    protected function generateWithOpenRouter(string $topic, string $type, ?string $customInstructions, string $apiKey, string $model, string $systemPrompt, float $temperature): ?string
    {
        $prompt = "Task: Write a {$type} about '{$topic}'.";
        if ($customInstructions) {
            $prompt .= "\n\nSpecific User Instructions: {$customInstructions}";
        }
        $prompt .= "\n\nFormat: Provide formatted text (HTML allowed if necessary) but ONLY return the content. Do not include markdown code blocks.";

        return $this->callOpenRouter($apiKey, $model, $systemPrompt, $prompt, $temperature);
    }

    protected function improveWithOpenRouter(string $content, ?string $instructions, string $apiKey, string $model, string $systemPrompt, float $temperature): ?string
    {
        $prompt = "Task: Improve the following content to be more professional, engaging, and clear.\n\n";
        $prompt .= "Original Content:\n{$content}\n\n";
        if ($instructions) {
            $prompt .= "Specific User Instructions: {$instructions}\n\n";
        }
        $prompt .= "Format: ONLY return the improved text. Do not explain what you changed. Keep the same HTML structure if present.";

        return $this->callOpenRouter($apiKey, $model, $systemPrompt, $prompt, $temperature);
    }

    protected function translateWithOpenRouter(string $content, string $targetLanguage, string $apiKey, string $model, float $temperature): ?string
    {
        $prompt = "Task: Translate the following content into {$targetLanguage}.\n\n";
        $prompt .= "Content:\n{$content}\n\n";
        $prompt .= "Format: ONLY return the translated text. Preserve names, construction terms, numbers, punctuation, and any HTML tags. Do not explain the translation.";

        return $this->callOpenRouter($apiKey, $model, 'You are a precise professional translator.', $prompt, $temperature);
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

    protected function translateWithOllama(string $content, string $targetLanguage, string $baseUrl, string $model, float $temperature): ?string
    {
        $url = rtrim($baseUrl, '/') . '/api/generate';

        $prompt = "Task: Translate the following content into {$targetLanguage}.\n\nContent: {$content}\n\nReturn ONLY the translated text. Preserve names, construction terms, numbers, punctuation, and any HTML tags.";

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

    protected function callOpenRouter(string $apiKey, string $model, string $systemPrompt, string $prompt, float $temperature): ?string
    {
        $response = Http::withToken($apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-OpenRouter-Title' => config('app.name', 'Kimmex'),
            ])
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $temperature,
            ]);

        if ($response->successful()) {
            $text = $response->json('choices.0.message.content');
            $text = preg_replace('/^```(?:html)?\n/s', '', (string) $text);
            $text = preg_replace('/\n```$/s', '', $text);

            return trim($text);
        }

        $error = $response->json('error.message') ?? $response->body();
        throw new Exception("OpenRouter Error: " . $error);
    }

    public function getAvailableModels(?string $apiKey = null, ?string $provider = 'gemini', ?string $baseUrl = null): array
    {
        $settings = SystemSetting::get('ai_settings', []);
        $providerConfig = $this->providerConfig($settings, $provider ?? 'gemini');
        $apiKey = $apiKey ?: $providerConfig['api_key'];
        $baseUrl = $baseUrl ?: $providerConfig['base_url'];
        
        try {
            if ($provider === 'gemini') {
                if (empty($apiKey)) {
                    return $this->defaultModels('gemini');
                }
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

            if ($provider === 'openrouter') {
                if (empty($apiKey)) {
                    return $this->defaultModels('openrouter');
                }

                $response = Http::withToken($apiKey)->get('https://openrouter.ai/api/v1/models');
                if ($response->successful()) {
                    $models = collect($response->json('data') ?? [])
                        ->filter(fn ($m) => in_array('text', $m['architecture']['input_modalities'] ?? ['text'], true))
                        ->mapWithKeys(function ($m) {
                            $id = $m['id'] ?? null;
                            $name = $m['name'] ?? $id;
                            $pricing = $m['pricing'] ?? [];
                            $isFree = ($pricing['prompt'] ?? null) === '0' && ($pricing['completion'] ?? null) === '0';

                            return $id ? [$id => $name . ($isFree ? ' (Free)' : '')] : [];
                        })
                        ->toArray();

                    return $models ?: $this->defaultModels('openrouter');
                }
            }
        } catch (Exception $e) {
            return $this->defaultModels($provider ?? 'gemini');
        }

        return $this->defaultModels($provider ?? 'gemini');
    }

    protected function providerConfig(array $settings, string $provider): array
    {
        $gemini = $settings['gemini'] ?? [];
        $openrouter = $settings['openrouter'] ?? [];
        $ollama = $settings['ollama'] ?? [];

        return [
            'api_key' => match ($provider) {
                'gemini' => $gemini['api_key'] ?? $settings['api_key'] ?? '',
                'openrouter' => $openrouter['api_key'] ?? '',
                default => $settings['api_key'] ?? '',
            },
            'model' => match ($provider) {
                'ollama' => $ollama['model'] ?? $settings['model'] ?? 'llama3.1',
                'openrouter' => $openrouter['model'] ?? 'deepseek/deepseek-chat-v3-0324:free',
                default => $gemini['model'] ?? $settings['model'] ?? 'gemini-3.1-flash-lite',
            },
            'base_url' => $provider === 'ollama'
                ? ($ollama['base_url'] ?? $settings['base_url'] ?? 'http://localhost:11434')
                : ($settings['base_url'] ?? 'http://localhost:11434'),
        ];
    }

    protected function defaultModels(string $provider): array
    {
        if ($provider === 'ollama') {
            return [
                'llama3.1' => 'llama3.1',
                'qwen2.5' => 'qwen2.5',
                'mistral' => 'mistral',
            ];
        }

        if ($provider === 'openrouter') {
            return [
                'deepseek/deepseek-chat-v3-0324:free' => 'DeepSeek Chat V3 0324 (Free)',
                'qwen/qwen3-235b-a22b:free' => 'Qwen3 235B A22B (Free)',
                'meta-llama/llama-3.3-70b-instruct:free' => 'Llama 3.3 70B Instruct (Free)',
                'google/gemini-2.0-flash-exp:free' => 'Gemini 2.0 Flash Experimental (Free)',
            ];
        }

        return [
            'gemini-3.1-flash-lite' => 'Gemini 3.1 Flash-Lite (Free, recommended)',
            'gemini-3.5-flash' => 'Gemini 3.5 Flash',
            'models/gemini-3.1-flash-lite' => 'Gemini 3.1 Flash-Lite',
            'models/gemini-3.5-flash' => 'Gemini 3.5 Flash',
        ];
    }
}
