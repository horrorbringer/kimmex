<?php

namespace App\Livewire;

use App\Models\SystemSetting;
use App\Services\AIGeneratorService;
use Filament\Notifications\Notification;
use Livewire\Component;

class AiSwitcher extends Component
{
    public string $provider = 'gemini';

    public string $model = '';

    public array $availableModels = [];

    public function mount()
    {
        $settings = SystemSetting::get('ai_settings', []);
        $this->provider = $settings['provider'] ?? 'gemini';
        $this->model = $this->modelForProvider($settings, $this->provider);
        $this->loadModels();
    }

    public function loadModels()
    {
        try {
            $service = new AIGeneratorService;
            $settings = SystemSetting::get('ai_settings', []);
            $apiKey = $this->apiKeyForProvider($settings, $this->provider);
            $baseUrl = $settings['ollama']['base_url'] ?? $settings['base_url'] ?? 'http://localhost:11434';

            if ($this->provider === 'gemini' && empty($apiKey)) {
                $this->availableModels = ['gemini-3.1-flash-lite' => 'Gemini 3.1 Flash-Lite (Free, recommended)'];

                return;
            }

            $this->availableModels = $service->getAvailableModels(
                in_array($this->provider, ['gemini', 'openrouter'], true) ? $apiKey : null,
                $this->provider,
                $this->provider === 'ollama' ? $baseUrl : null
            );

            // If current model is not in available list, reset to first one
            if (! empty($this->availableModels) && ! isset($this->availableModels[$this->model])) {
                $this->model = array_key_first($this->availableModels);
            }
        } catch (\Exception $e) {
            $this->availableModels = [];
        }
    }

    public function switchProvider(string $newProvider)
    {
        $settings = SystemSetting::get('ai_settings', []);
        $settings['provider'] = $newProvider;

        // Use the saved model for this provider if it exists
        $this->model = $this->modelForProvider($settings, $newProvider);

        SystemSetting::set('ai_settings', $settings);
        $this->provider = $newProvider;
        $this->loadModels();

        Notification::make()
            ->title(__('AI Provider Switched'))
            ->body(__('Now using '.$this->providerLabel($newProvider)))
            ->success()
            ->send();
    }

    public function updatedModel($value)
    {
        $settings = SystemSetting::get('ai_settings', []);
        $providerSettings = $settings[$this->provider] ?? [];
        $providerSettings['model'] = $value;
        $settings[$this->provider] = $providerSettings;
        SystemSetting::set('ai_settings', $settings);

        Notification::make()
            ->title(__('AI Model Updated'))
            ->body(__('Now using '.$value))
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.ai-switcher');
    }

    public function nextProvider(): string
    {
        return match ($this->provider) {
            'gemini' => 'openrouter',
            'openrouter' => 'ollama',
            default => 'gemini',
        };
    }

    protected function modelForProvider(array $settings, string $provider): string
    {
        return match ($provider) {
            'ollama' => $settings['ollama']['model'] ?? $settings['model'] ?? '',
            'openrouter' => $settings['openrouter']['model'] ?? 'deepseek/deepseek-chat-v3-0324:free',
            default => $settings['gemini']['model'] ?? $settings['model'] ?? 'gemini-3.1-flash-lite',
        };
    }

    protected function apiKeyForProvider(array $settings, string $provider): string
    {
        return match ($provider) {
            'openrouter' => $settings['openrouter']['api_key'] ?? '',
            'gemini' => $settings['gemini']['api_key'] ?? $settings['api_key'] ?? '',
            default => '',
        };
    }

    protected function providerLabel(string $provider): string
    {
        return match ($provider) {
            'openrouter' => 'OpenRouter',
            'ollama' => 'Ollama',
            default => 'Google Gemini',
        };
    }
}
