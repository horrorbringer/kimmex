<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use App\Services\AutoTranslateService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Slider;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Placeholder;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-8-tooth';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('Global Configuration');
    }

    public function getTitle(): string
    {
        return __('System-Wide Settings');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];
    public array $availableModels = [];

    public function mount(): void
    {
        $org = SystemSetting::get('organization_profile', []);
        $brand = SystemSetting::get('brand_identity', []);
        $ai = SystemSetting::get('ai_settings', []);
        $integration = SystemSetting::get('integration_settings', []);
        $theme = SystemSetting::get('theme_settings', []);

        $this->data = [
            // Organization
            'company_name' => $org['en']['company_name'] ?? 'Kimmex Construction',
            'tagline' => $org['en']['tagline'] ?? '',
            'registration_number' => $org['registration_number'] ?? '',
            'founded_date' => $org['founded_date'] ?? '',
            'phone' => $org['phone'] ?? '',
            'email' => $org['email'] ?? '',
            'working_hours' => $org['en']['working_hours'] ?? '',
            'address' => $org['en']['address'] ?? '',
            'google_maps_url' => $org['google_maps_url'] ?? '',
            'logo' => $org['logo'] ?? '',
            
            // Social
            'facebook' => $org['facebook'] ?? '',
            'linkedin' => $org['linkedin'] ?? '',
            'youtube' => $org['youtube'] ?? '',
            'instagram' => $org['instagram'] ?? '',
            'telegram' => $org['telegram'] ?? '',

            // Branding
            'ceo_name' => $brand['ceo_name'] ?? '',
            'company_story' => $brand['en']['company_story'] ?? '',
            'ceo_message' => $brand['en']['ceo_message'] ?? '',
            'mission' => $brand['en']['mission'] ?? '',
            'vision' => $brand['en']['vision'] ?? '',
            'goal' => $brand['en']['goal'] ?? '',
            'values' => $brand['en']['values_list'] ?? [],

            // AI
            'ai_provider' => $ai['provider'] ?? 'gemini',
            'gemini_api_key' => $ai['gemini']['api_key'] ?? ($ai['api_key'] ?? ''),
            'gemini_model' => $ai['gemini']['model'] ?? ($ai['model'] ?? 'gemini-1.5-flash'),
            'ollama_base_url' => $ai['ollama']['base_url'] ?? ($ai['base_url'] ?? 'http://localhost:11434'),
            'ollama_model' => $ai['ollama']['model'] ?? ($ai['model'] ?? ''),
            'ai_system_prompt' => $ai['system_prompt'] ?? 'You are an expert copywriter and corporate communications specialist. Write engaging, professional, and SEO-optimized content. Return pure text or basic HTML without markdown blocks.',
            'ai_temperature' => $ai['temperature'] ?? 0.7,
            'ai_tone' => $ai['tone'] ?? 'professional',
            'auto_translate' => $ai['auto_translate'] ?? true,
            
            // Integration
            'tg_enabled' => $integration['telegram_enabled'] ?? false,
            'tg_bot_token' => $integration['telegram_bot_token'] ?? '',
            'tg_chat_id' => $integration['telegram_chat_id'] ?? '',
            
            // Appearance
            'primary_color' => $theme['primary_color'] ?? '#fbbf24',
            'secondary_color' => $theme['secondary_color'] ?? '#1e293b',
            'font_en' => $theme['font_en'] ?? 'Inter',
            'font_kh' => $theme['font_kh'] ?? 'Kantumruy Pro',
        ];

        $provider = $this->data['ai_provider'];
        $this->availableModels = (new \App\Services\AIGeneratorService())->getAvailableModels(
            $provider === 'gemini' ? $this->data['gemini_api_key'] : null,
            $provider,
            $provider === 'ollama' ? $this->data['ollama_base_url'] : null
        );

        $currentModel = $provider === 'gemini' ? $this->data['gemini_model'] : $this->data['ollama_model'];
        if (!empty($currentModel) && !isset($this->availableModels[$currentModel])) {
            $this->availableModels[$currentModel] = $currentModel;
        }

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('SettingsHub')
                    ->tabs([
                        // --- 1. ORGANIZATION TAB ---
                        Tab::make(__('Organization'))
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Grid::make(2)->schema([
                                    Section::make(__('Identity & Brand'))
                                        ->description(__('Manage your logo, name, and catchphrase.'))
                                        ->columnSpan(1)
                                        ->schema([
                                            FileUpload::make('logo')->image()->disk('public')->directory('organization'),
                                            TextInput::make('company_name')->label(__('Company Name'))->required(),
                                            TextInput::make('tagline')
                                                ->label(__('Tagline'))
                                                ->hintAction($this->getAiImproveAction('tagline', 'Improve this tagline for a construction company.')),
                                            TextInput::make('registration_number')->label(__('Registration #')),
                                            DatePicker::make('founded_date')->label(__('Founded Date')),
                                        ]),

                                    Section::make(__('Contact & Location'))
                                        ->description(__('How clients can reach you.'))
                                        ->columnSpan(1)
                                        ->schema([
                                            TextInput::make('email')->email()->label(__('Official Email')),
                                            TextInput::make('phone')->tel()->label(__('Contact Phone')),
                                            TextInput::make('working_hours')->label(__('Office Hours')),
                                            Textarea::make('address')->rows(3)->label(__('Physical Address')),
                                            TextInput::make('google_maps_url')->url()->label(__('Google Maps Link')),
                                        ]),
                                ]),

                                Section::make(__('Executive Message & Bio'))
                                    ->schema([
                                        TextInput::make('ceo_name')->label(__('CEO Name')),
                                        RichEditor::make('ceo_message')->label(__('CEO Official Message'))->columnSpanFull(),
                                        Textarea::make('company_story')
                                            ->label(__('Our Story'))
                                            ->rows(4)
                                            ->hintAction($this->getAiImproveAction('company_story', 'Make this company story more inspiring and professional.')),
                                    ])->collapsible(),
                                
                                Section::make(__('Mission, Vision & Goals'))
                                    ->columns(3)
                                    ->schema([
                                        Textarea::make('mission')->rows(3)->hintAction($this->getAiImproveAction('mission')),
                                        Textarea::make('vision')->rows(3)->hintAction($this->getAiImproveAction('vision')),
                                        Textarea::make('goal')->rows(3)->hintAction($this->getAiImproveAction('goal')),
                                    ])->collapsible()->collapsed(),
                                
                                Section::make(__('Core Values'))
                                    ->schema([
                                        Repeater::make('values')
                                            ->simple(TextInput::make('value'))
                                            ->reorderable()
                                            ->collapsible(),
                                    ])->collapsible()->collapsed(),
                            ]),

                        // --- 2. AI ENGINE TAB ---
                        Tab::make(__('AI Engine'))
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                // Health Dashboard
                                Placeholder::make('ai_health_monitor')
                                    ->hiddenLabel()
                                    ->content(view('filament.components.ai-stats-card')),

                                Grid::make(2)->schema([
                                    Section::make(__('Provider Config'))
                                        ->columnSpan(1)
                                        ->schema([
                                            Select::make('ai_provider')
                                                ->label(__('Active AI Provider'))
                                                ->options([
                                                    'gemini' => 'Google Gemini (Cloud)',
                                                    'ollama' => 'Ollama (Local)',
                                                ])
                                                ->required()->live(),
                                            
                                            // Gemini Fields
                                            Group::make()
                                                ->visible(fn ($get) => $get('ai_provider') === 'gemini')
                                                ->schema([
                                                    TextInput::make('gemini_api_key')
                                                        ->label(__('Gemini API Key'))
                                                        ->password()->revealable()
                                                        ->hintAction(
                                                            \Filament\Actions\Action::make('fetchGeminiModels')
                                                                ->icon('heroicon-o-arrow-path')
                                                                ->action(function ($state, $get, $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels($state, 'gemini');
                                                                    Notification::make()->title(__('Gemini Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('gemini_model')
                                                        ->label(__('Active Model'))
                                                        ->options(fn() => $this->availableModels)->searchable(),
                                                ]),

                                            // Ollama Fields
                                            Group::make()
                                                ->visible(fn ($get) => $get('ai_provider') === 'ollama')
                                                ->schema([
                                                    TextInput::make('ollama_base_url')
                                                        ->label(__('Ollama Base URL'))
                                                        ->placeholder('http://localhost:11434')
                                                        ->hintAction(
                                                            \Filament\Actions\Action::make('fetchOllamaModels')
                                                                ->icon('heroicon-o-arrow-path')
                                                                ->action(function ($state, $get, $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels(null, 'ollama', $state);
                                                                    Notification::make()->title(__('Ollama Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('ollama_model')
                                                        ->label(__('Active Model'))
                                                        ->options(fn() => $this->availableModels)->searchable(),
                                                ]),

                                            Toggle::make('auto_translate')
                                                ->label(__('Enable AI Auto-Translation'))
                                                ->helperText(__('Generate Khmer translations automatically when saving.'))
                                                ->default(true),
                                        ]),

                                    Section::make(__('Persona & Style'))
                                        ->columnSpan(1)
                                        ->schema([
                                            Select::make('ai_tone')
                                                ->label(__('Tone of Voice'))
                                                ->options([
                                                    'professional' => 'Professional/Corporate',
                                                    'friendly' => 'Friendly/Approachable',
                                                    'marketing' => 'Urgent/Marketing-focused',
                                                ]),
                                            Slider::make('ai_temperature')
                                                ->label(__('Creativity (Temperature)'))
                                                ->minValue(0.1)->maxValue(1.0)->step(0.1),
                                            Textarea::make('ai_system_prompt')
                                                ->label(__('Global System Instructions'))
                                                ->rows(5),
                                        ]),
                                ]),

                                Section::make(__('AI Playground (Testing Area)'))
                                    ->collapsible()->collapsed()
                                    ->icon('heroicon-o-beaker')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('test_topic')->placeholder(__('e.g. Benefits of sustainable construction')),
                                            Select::make('test_type')->options([
                                                'article' => 'Short Article',
                                                'tagline' => 'Brand Tagline',
                                                'story' => 'Company Story',
                                            ])->default('article'),
                                        ]),
                                        Textarea::make('test_result')->rows(5)->readOnly()->placeholder(__('Result will appear here...')),
                                        \Filament\Schemas\Components\Actions::make([
                                            \Filament\Actions\Action::make('runTest')
                                                ->label(__('Generate Test Content'))
                                                ->icon('heroicon-m-play')
                                                ->action(function ($get, $set, \App\Services\AIGeneratorService $ai) {
                                                    $topic = $get('test_topic');
                                                    if (empty($topic)) return;
                                                    try {
                                                        $provider = $get('ai_provider');
                                                        $settings = [
                                                            'provider' => $provider,
                                                            'api_key' => $get('gemini_api_key'),
                                                            'model' => $provider === 'gemini' ? $get('gemini_model') : $get('ollama_model'),
                                                            'base_url' => $get('ollama_base_url'),
                                                            'system_prompt' => $get('ai_system_prompt'),
                                                            'temperature' => $get('ai_temperature'),
                                                            'tone' => $get('ai_tone'),
                                                        ];
                                                        $set('test_result', $ai->generateContent($topic, $get('test_type'), null, $settings));
                                                        Notification::make()->title(__('Success'))->success()->send();
                                                    } catch (\Exception $e) {
                                                        Notification::make()->title(__('Error'))->body($e->getMessage())->danger()->send();
                                                    }
                                                }),
                                        ]),
                                    ]),
                            ]),

                        // --- 3. INTEGRATIONS TAB ---
                        Tab::make(__('Integrations'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make(__('Social Media Links'))
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('facebook')->url()->prefix('https://'),
                                        TextInput::make('linkedin')->url()->prefix('https://'),
                                        TextInput::make('youtube')->url()->prefix('https://'),
                                        TextInput::make('instagram')->url()->prefix('https://'),
                                        TextInput::make('telegram')->url()->prefix('https://t.me/'),
                                    ]),
                                Section::make(__('Telegram Bot Alerts'))
                                    ->schema([
                                        Toggle::make('tg_enabled')->label(__('Enable Bot Notifications')),
                                        TextInput::make('tg_bot_token')->password()->revealable()->label(__('Bot Token')),
                                        TextInput::make('tg_chat_id')->label(__('Admin Chat ID')),
                                    ]),
                            ]),

                        // --- 4. APPEARANCE TAB ---
                        Tab::make(__('Appearance'))
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make(__('Brand Colors'))
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('primary_color')->label(__('Primary Accent')),
                                        ColorPicker::make('secondary_color')->label(__('Secondary Color')),
                                    ]),
                                Section::make(__('Typography'))
                                    ->columns(2)
                                    ->schema([
                                        Select::make('font_en')->label(__('Latin Font (EN)'))->options([
                                            'Inter' => 'Inter',
                                            'Roboto' => 'Roboto',
                                            'Outfit' => 'Outfit',
                                        ]),
                                        Select::make('font_kh')->label(__('Khmer Font (KH)'))->options([
                                            'Kantumruy Pro' => 'Kantumruy Pro',
                                            'Hanuman' => 'Hanuman',
                                            'Moul' => 'Moul',
                                        ]),
                                    ]),
                            ]),
                    ])
            ])
            ->statePath('data');
    }

    protected function getAiImproveAction(string $field, ?string $prompt = null): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('aiImprove' . ucfirst($field))
            ->icon('heroicon-m-sparkles')
            ->tooltip(__('Improve with AI'))
            ->action(function ($state, $get, \Filament\Schemas\Components\Utilities\Set $set, \App\Services\AIGeneratorService $ai) use ($field, $prompt) {
                if (empty($state)) return;
                $settings = [
                    'provider' => $get('ai_provider'),
                    'api_key' => $get('gemini_api_key'),
                    'model' => $get('ai_provider') === 'gemini' ? $get('gemini_model') : $get('ollama_model'),
                    'base_url' => $get('ollama_base_url'),
                    'system_prompt' => $get('ai_system_prompt'),
                    'temperature' => $get('ai_temperature'),
                    'tone' => $get('ai_tone'),
                ];
                $set($field, $ai->improveContent($state, $prompt ?? 'Improve this text.', $settings));
                Notification::make()->title(__('Content Improved'))->success()->send();
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save All Configuration'))
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $translator = new AutoTranslateService();
        $autoTranslate = $state['auto_translate'] ?? true;

        // 1. Organization Profile
        $orgEn = [
            'company_name' => $state['company_name'],
            'tagline' => $state['tagline'],
            'address' => $state['address'],
            'working_hours' => $state['working_hours'],
        ];

        $orgKm = $autoTranslate 
            ? $translator->translateArray($orgEn, [], 'km')
            : (SystemSetting::get('organization_profile')['km'] ?? []);

        SystemSetting::set('organization_profile', [
            'registration_number' => $state['registration_number'],
            'founded_date' => $state['founded_date'],
            'phone' => $state['phone'],
            'email' => $state['email'],
            'google_maps_url' => $state['google_maps_url'],
            'logo' => $state['logo'],
            'facebook' => $state['facebook'],
            'linkedin' => $state['linkedin'],
            'youtube' => $state['youtube'],
            'instagram' => $state['instagram'],
            'telegram' => $state['telegram'],
            'en' => $orgEn,
            'km' => $orgKm,
        ]);

        // 2. Brand Identity
        $brandEn = [
            'company_story' => $state['company_story'],
            'ceo_message' => $state['ceo_message'],
            'mission' => $state['mission'],
            'vision' => $state['vision'],
            'goal' => $state['goal'],
            'values_list' => $state['values'],
        ];

        $brandKm = $autoTranslate
            ? $translator->translateArray($brandEn, [], 'km')
            : (SystemSetting::get('brand_identity')['km'] ?? []);

        SystemSetting::set('brand_identity', [
            'ceo_name' => $state['ceo_name'],
            'en' => $brandEn,
            'km' => $brandKm,
        ]);

        // 3. AI Settings (Multi-Provider)
        SystemSetting::set('ai_settings', [
            'provider' => $state['ai_provider'],
            'gemini' => [
                'api_key' => $state['gemini_api_key'],
                'model' => $state['gemini_model'],
            ],
            'ollama' => [
                'base_url' => $state['ollama_base_url'],
                'model' => $state['ollama_model'],
            ],
            'system_prompt' => $state['ai_system_prompt'],
            'temperature' => $state['ai_temperature'],
            'tone' => $state['ai_tone'],
            'auto_translate' => $state['auto_translate'],
        ]);

        // 4. Integration Settings
        SystemSetting::set('integration_settings', [
            'telegram_enabled' => $state['tg_enabled'],
            'telegram_bot_token' => $state['tg_bot_token'],
            'telegram_chat_id' => $state['tg_chat_id'],
        ]);

        // 5. Theme Settings
        SystemSetting::set('theme_settings', [
            'primary_color' => $state['primary_color'],
            'secondary_color' => $state['secondary_color'],
            'font_en' => $state['font_en'],
            'font_kh' => $state['font_kh'],
        ]);

        Notification::make()
            ->title(__('Global Configuration Saved'))
            ->body(__('All settings, including advanced AI persona and translations, have been updated.'))
            ->success()
            ->send();
    }
}
