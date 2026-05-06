<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;

class ManageAISettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cpu-chip';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public function getTitle(): string
    {
        return __('AI Generator Settings');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.manage-ai-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SystemSetting::get('ai_settings', []);

        $this->data = [
            'provider' => $settings['provider'] ?? 'gemini',
            'api_key' => $settings['api_key'] ?? '',
            'model' => $settings['model'] ?? 'gemini-1.5-flash',
            'system_prompt' => $settings['system_prompt'] ?? 'You are an expert copywriter and corporate communications specialist. Write engaging, professional, and SEO-optimized content. Return pure text or basic HTML without markdown blocks.',
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('AI Integration Configuration'))
                    ->description(__('Configure your preferred AI provider to enable automatic content generation across the admin panel.'))
                    ->schema([
                        Select::make('provider')
                            ->label(__('AI Provider'))
                            ->options([
                                'gemini' => 'Google Gemini (Free Tier available)',
                                'openai' => 'OpenAI ChatGPT',
                            ])
                            ->default('gemini')
                            ->required(),
                        
                        TextInput::make('api_key')
                            ->label(__('API Key'))
                            ->password()
                            ->revealable()
                            ->helperText(__('Get a free API key from Google AI Studio (aistudio.google.com).'))
                            ->required(),

                        TextInput::make('model')
                            ->label(__('AI Model'))
                            ->default('gemini-1.5-flash')
                            ->helperText(__('Example: gemini-1.5-flash or gpt-4o-mini'))
                            ->required(),
                    ]),

                Section::make(__('AI Behavior & Prompts'))
                    ->description(__('Customize how the AI should write and behave by default.'))
                    ->schema([
                        Textarea::make('system_prompt')
                            ->label(__('System Prompt / Persona'))
                            ->rows(4)
                            ->required()
                            ->helperText(__('This instruction is sent with every generation request to ensure the AI uses the correct tone and formatting.')),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save Settings'))
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        SystemSetting::set('ai_settings', $state);

        Notification::make()
            ->title('AI Settings Saved Successfully')
            ->success()
            ->send();
    }
}
