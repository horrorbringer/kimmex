<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SystemSetting;
use Filament\Notifications\Notification;

class AISwitcher extends Component
{
    public string $provider = 'gemini';
    public string $model = '';
    public array $availableModels = [];

    public function mount()
    {
        $settings = SystemSetting::get('ai_settings', []);
        $this->provider = $settings['provider'] ?? 'gemini';
        $this->model = $this->provider === 'ollama'
            ? ($settings['ollama']['model'] ?? $settings['model'] ?? '')
            : ($settings['gemini']['model'] ?? $settings['model'] ?? '');
        $this->loadModels();
    }

    public function loadModels()
    {
        try {
            $service = new \App\Services\AIGeneratorService();
            $settings = SystemSetting::get('ai_settings', []);
            $apiKey = $settings['gemini']['api_key'] ?? $settings['api_key'] ?? '';
            $baseUrl = $settings['ollama']['base_url'] ?? $settings['base_url'] ?? 'http://localhost:11434';

            if ($this->provider === 'gemini' && empty($apiKey)) {
                $this->availableModels = ['gemini-1.5-flash' => 'Gemini 1.5 Flash (Default)'];
                return;
            }

            $this->availableModels = $service->getAvailableModels(
                $this->provider === 'gemini' ? $apiKey : $baseUrl, 
                $this->provider
            );

            // If current model is not in available list, reset to first one
            if (!empty($this->availableModels) && !isset($this->availableModels[$this->model])) {
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
        $this->model = $newProvider === 'ollama'
            ? ($settings['ollama']['model'] ?? $settings['model'] ?? '')
            : ($settings['gemini']['model'] ?? $settings['model'] ?? '');

        SystemSetting::set('ai_settings', $settings);
        $this->provider = $newProvider;
        $this->loadModels();

        Notification::make()
            ->title(__('AI Provider Switched'))
            ->body(__('Now using ' . ($newProvider === 'gemini' ? 'Google Gemini' : 'Ollama')))
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
            ->body(__('Now using ' . $value))
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.ai-switcher');
    }
}
