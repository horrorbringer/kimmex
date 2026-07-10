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
use Illuminate\Validation\ValidationException;
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
            'favicon' => $org['favicon'] ?? '',
            'website_title' => $org['en']['website_title'] ?? '',
            
            // Social
            'facebook' => $org['facebook'] ?? '',
            'linkedin' => $org['linkedin'] ?? '',
            'youtube' => $org['youtube'] ?? '',
            'instagram' => $org['instagram'] ?? '',
            'telegram' => $org['telegram'] ?? '',

            // Branding
            'ceo_name' => $brand['ceo_name'] ?? '',
            'about_hero_image' => $brand['about_hero_image'] ?? '',
            'about_section_image_1' => $brand['about_section_images'][0] ?? '',
            'about_section_image_2' => $brand['about_section_images'][1] ?? '',
            'about_section_image_3' => $brand['about_section_images'][2] ?? '',
            'about_section_image_4' => $brand['about_section_images'][3] ?? '',
            'company_story' => $brand['en']['company_story'] ?? '',
            'ceo_message' => $brand['en']['ceo_message'] ?? '',
            'mission' => $brand['en']['mission'] ?? '',
            'vision' => $brand['en']['vision'] ?? '',
            'goal' => $brand['en']['goal'] ?? '',
            'values' => $this->normalizeCoreValues($brand['en']['values_list'] ?? []),

            // AI
            'ai_provider' => $ai['provider'] ?? 'gemini',
            'gemini_api_key' => $ai['gemini']['api_key'] ?? ($ai['api_key'] ?? ''),
            'gemini_model' => $ai['gemini']['model'] ?? ($ai['model'] ?? 'gemini-3.1-flash-lite'),
            'openrouter_api_key' => $ai['openrouter']['api_key'] ?? '',
            'openrouter_model' => $ai['openrouter']['model'] ?? 'deepseek/deepseek-chat-v3-0324:free',
            'ollama_base_url' => $ai['ollama']['base_url'] ?? ($ai['base_url'] ?? 'http://localhost:11434'),
            'ollama_model' => $ai['ollama']['model'] ?? ($ai['model'] ?? ''),
            'ai_system_prompt' => $ai['system_prompt'] ?? 'You are an expert copywriter and corporate communications specialist. Write engaging, professional, and SEO-optimized content. Return pure text or basic HTML without markdown blocks.',
            'ai_temperature' => $ai['temperature'] ?? 0.7,
            'ai_tone' => $ai['tone'] ?? 'professional',
            'auto_translate' => $ai['auto_translate'] ?? false,
            
            // Integration
            'telegram_enabled' => (bool) ($integration['telegram_enabled'] ?? false),
            'telegram_bot_token' => $integration['telegram_bot_token'] ?? '',
            'telegram_jobs_chat_id' => $integration['telegram_jobs_chat_id'] ?? '',
            'telegram_inquiries_chat_id' => $integration['telegram_inquiries_chat_id'] ?? '',
            
            // Appearance
            'primary_color' => $theme['primary_color'] ?? '#E31E24',
            'primary_color_hover' => $theme['primary_color_hover'] ?? '#C8151D',
            'secondary_color' => $theme['secondary_color'] ?? '#1a1a2e',
            'secondary_color_hover' => $theme['secondary_color_hover'] ?? '#0E3A7A',
            'font_en' => $theme['font_family_en'] ?? 'Droid Serif',
            'font_kh' => $theme['font_family_km'] ?? 'Suwannaphum',
            'footer_bg_color' => $theme['footer_bg_color'] ?? '#071A33',
            'footer_accent_color' => $theme['footer_accent_color'] ?? '#ED1C24',
            'news_page_bg_color' => $theme['news_page_bg_color'] ?? '#F7F8FA',
        ];

        $provider = $this->data['ai_provider'];
        $this->availableModels = (new \App\Services\AIGeneratorService())->getAvailableModels(
            match ($provider) {
                'gemini' => $this->data['gemini_api_key'],
                'openrouter' => $this->data['openrouter_api_key'],
                default => null,
            },
            $provider,
            $provider === 'ollama' ? $this->data['ollama_base_url'] : null
        );

        $currentModel = match ($provider) {
            'ollama' => $this->data['ollama_model'],
            'openrouter' => $this->data['openrouter_model'],
            default => $this->data['gemini_model'],
        };
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
                                        ->description(__('Manage your logo, favicon, name, and catchphrase.'))
                                        ->columnSpan(1)
                                        ->schema([
                                            Grid::make(2)->schema([
                                                $this->makeImageUpload('logo', __('Logo'), 'organization')
                                                    ->helperText(__('PNG, JPG, WebP, or SVG. Maximum size: 5 MB.')),
                                                $this->makeImageUpload('favicon', __('Favicon'), 'organization')
                                                    ->helperText(__('PNG, ICO, JPG, WebP, or SVG. Maximum size: 2 MB.'))
                                                    ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                                                    ->maxSize(2048),
                                            ]),
                                            TextInput::make('company_name')->label(__('Company Name'))->required(),
                                            TextInput::make('website_title')
                                                ->label(__('Website Title'))
                                                ->helperText(__('Custom title used for browser tabs and SEO.')),
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
                                        RichEditor::make('ceo_message')->label(__('CEO Official Message'))->columnSpanFull()->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))->fileAttachmentsVisibility('public'),
                                        Textarea::make('company_story')
                                            ->label(__('Our Story'))
                                            ->rows(4)
                                            ->hintAction($this->getAiImproveAction('company_story', 'Make this company story more inspiring and professional.')),
                                    ])->collapsible(),

                                Section::make(__('About Page Images'))
                                    ->description(__('Manage the About page hero image and the four images beside the Who We Are section.'))
                                    ->columns(2)
                                    ->schema([
                                        $this->makeImageUpload('about_hero_image', __('About Hero Image'), 'brand/about')
                                            ->helperText(__('Used as the large background image at the top of the About page.'))
                                            ->columnSpanFull(),
                                        $this->makeImageUpload('about_section_image_1', __('Who We Are Image 1'), 'brand/about'),
                                        $this->makeImageUpload('about_section_image_2', __('Who We Are Image 2'), 'brand/about'),
                                        $this->makeImageUpload('about_section_image_3', __('Who We Are Image 3'), 'brand/about'),
                                        $this->makeImageUpload('about_section_image_4', __('Who We Are Image 4'), 'brand/about'),
                                    ])
                                    ->collapsible(),
                                
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
                                            ->label(__('Core Values'))
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label(__('Image'))
                                                    ->image()
                                                    ->disk(config('filesystems.public_uploads_disk'))
                                                    ->directory('brand/core-values')
                                                    ->visibility('public')
                                                    ->columnSpanFull(),
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): string => filled($state['image'] ?? null) ? __('Image') : __('New image')),
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
                                                    'openrouter' => 'OpenRouter (Cloud)',
                                                    'ollama' => 'Ollama (Local)',
                                                ])
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function ($state, $get, $set, \App\Services\AIGeneratorService $ai) {
                                                    $this->availableModels = $ai->getAvailableModels(
                                                        match ($state) {
                                                            'gemini' => $get('gemini_api_key'),
                                                            'openrouter' => $get('openrouter_api_key'),
                                                            default => null,
                                                        },
                                                        $state,
                                                        $state === 'ollama' ? $get('ollama_base_url') : null
                                                    );

                                                    $modelField = match ($state) {
                                                        'openrouter' => 'openrouter_model',
                                                        'ollama' => 'ollama_model',
                                                        default => 'gemini_model',
                                                    };

                                                    if (! $get($modelField) && $this->availableModels !== []) {
                                                        $set($modelField, array_key_first($this->availableModels));
                                                    }
                                                }),
                                            
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
                                                                ->action(function ($state, $get, \App\Services\AIGeneratorService $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels($state, 'gemini');
                                                                    \Filament\Notifications\Notification::make()->title(__('Gemini Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('gemini_model')
                                                        ->label(__('Active Model'))
                                                        ->options(fn() => $this->availableModels)->searchable(),
                                                ]),

                                            // OpenRouter Fields
                                            Group::make()
                                                ->visible(fn ($get) => $get('ai_provider') === 'openrouter')
                                                ->schema([
                                                    TextInput::make('openrouter_api_key')
                                                        ->label(__('OpenRouter API Key'))
                                                        ->password()->revealable()
                                                        ->helperText(__('Use an OpenRouter key. Free models usually end with :free.'))
                                                        ->hintAction(
                                                            \Filament\Actions\Action::make('fetchOpenRouterModels')
                                                                ->icon('heroicon-o-arrow-path')
                                                                ->action(function ($state, $get, \App\Services\AIGeneratorService $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels($state, 'openrouter');
                                                                    \Filament\Notifications\Notification::make()->title(__('OpenRouter Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('openrouter_model')
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
                                                                ->action(function ($state, $get, \App\Services\AIGeneratorService $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels(null, 'ollama', $state);
                                                                    \Filament\Notifications\Notification::make()->title(__('Ollama Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('ollama_model')
                                                        ->label(__('Active Model'))
                                                        ->options(fn() => $this->availableModels)->searchable(),
                                                ]),

                                            Toggle::make('auto_translate')
                                                ->label(__('Enable AI Auto-Translation'))
                                                ->helperText(__('Automatically generate Khmer translations on save. Disable on shared hosting — enable only if you have a queue worker running.'))
                                                ->default(false),
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
                                                            'gemini' => [
                                                                'api_key' => $get('gemini_api_key'),
                                                                'model' => $get('gemini_model'),
                                                            ],
                                                            'openrouter' => [
                                                                'api_key' => $get('openrouter_api_key'),
                                                                'model' => $get('openrouter_model'),
                                                            ],
                                                            'ollama' => [
                                                                'base_url' => $get('ollama_base_url'),
                                                                'model' => $get('ollama_model'),
                                                            ],
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
                                        Toggle::make('telegram_enabled')->label(__('Enable Bot Notifications'))->live(),
                                        Group::make()
                                            ->visible(fn ($get) => $get('telegram_enabled'))
                                            ->schema([
                                                TextInput::make('telegram_bot_token')
                                                    ->label(__('Bot Token'))
                                                    ->password()
                                                    ->revealable()
                                                    ->helperText('Get this from @BotFather'),
                                                
                                                Grid::make(2)->schema([
                                                    TextInput::make('telegram_jobs_chat_id')
                                                        ->label(__('HR Chat ID (Jobs)'))
                                                        ->placeholder('-100123456789')
                                                        ->hint(fn($get) => $get('jobs_chat_title') ? '✅ ' . $get('jobs_chat_title') : null)
                                                        ->hintColor('success')
                                                        ->helperText(__('For job application alerts')),

                                                    TextInput::make('telegram_inquiries_chat_id')
                                                        ->label(__('Sales Chat ID (Inquiries)'))
                                                        ->placeholder('-100987654321')
                                                        ->hint(fn($get) => $get('inquiries_chat_title') ? '✅ ' . $get('inquiries_chat_title') : null)
                                                        ->hintColor('success')
                                                        ->helperText(__('For general inquiry alerts')),
                                                ]),

                                                \Filament\Schemas\Components\Actions::make([
                                                    \Filament\Actions\Action::make('testTelegram')
                                                        ->label(__('Verify & Test Connection'))
                                                        ->icon('heroicon-o-check-badge')
                                                        ->color('success')
                                                        ->action(function ($state, $get, $set) {
                                                            $token = $get('telegram_bot_token');
                                                            $targets = [
                                                                'jobs' => $get('telegram_jobs_chat_id'),
                                                                'inquiries' => $get('telegram_inquiries_chat_id'),
                                                            ];
                                                            
                                                            if (!$token || (!array_filter($targets))) {
                                                                \Filament\Notifications\Notification::make()
                                                                    ->warning()
                                                                    ->title(__('Missing Config'))
                                                                    ->body(__('Please enter bot token and at least one chat ID.'))
                                                                    ->send();
                                                                return;
                                                            }

                                                            $successCount = 0;
                                                            foreach ($targets as $key => $chatId) {
                                                                if (!$chatId) continue;
                                                                try {
                                                                    // 1. Get Chat Info (Title)
                                                                    $response = \Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot{$token}/getChat", [
                                                                        'chat_id' => $chatId,
                                                                    ]);

                                                                    if ($response->successful()) {
                                                                        $chatData = $response->json('result');
                                                                        $title = $chatData['title'] ?? ($chatData['first_name'] ?? 'Private Chat');
                                                                        $set($key . '_chat_title', $title);
                                                                        
                                                                        // 2. Send Test Message
                                                                        \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                                                                            'chat_id' => $chatId,
                                                                            'text' => "🚀 *KIMMEX VERIFIED*\nThis chat is now connected for " . strtoupper($key) . " alerts!",
                                                                            'parse_mode' => 'Markdown',
                                                                        ]);
                                                                        $successCount++;
                                                                    }
                                                                } catch (\Exception $e) {
                                                                    \Illuminate\Support\Facades\Log::error("Telegram {$key} Verify Failed: " . $e->getMessage());
                                                                }
                                                            }

                                                            \Filament\Notifications\Notification::make()
                                                                ->success()
                                                                ->title(__('Verification Complete'))
                                                                ->body(__("Verified and sent test messages to :count chat(s).", ['count' => $successCount]))
                                                                ->send();
                                                        }),
                                                ]),
                                            ]),
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
                                        ColorPicker::make('primary_color_hover')->label(__('Primary Accent Hover')),
                                        ColorPicker::make('secondary_color')->label(__('Secondary Color')),
                                        ColorPicker::make('secondary_color_hover')->label(__('Secondary Color Hover')),
                                    ]),
                                Section::make(__('Footer Appearance'))
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('footer_bg_color')->label(__('Footer Background Color')),
                                        ColorPicker::make('footer_accent_color')->label(__('Footer Accent/Link Color')),
                                    ]),
                                Section::make(__('Page Backgrounds'))
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('news_page_bg_color')
                                            ->label(__('News Article Page Background'))
                                            ->helperText(__('Background color of the news article detail page.')),
                                    ]),
                                Section::make(__('Typography'))
                                    ->columns(2)
                                    ->schema([
                                        Select::make('font_en')->label(__('Latin Font (EN)'))->options([
                                            'Droid Serif' => 'Droid Serif',
                                            'Inter' => 'Inter',
                                            'Roboto' => 'Roboto',
                                            'Outfit' => 'Outfit',
                                        ]),
                                        Select::make('font_kh')->label(__('Khmer Font (KH)'))->options([
                                            'Suwannaphum' => 'Suwannaphum',
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
                    'gemini' => [
                        'api_key' => $get('gemini_api_key'),
                        'model' => $get('gemini_model'),
                    ],
                    'openrouter' => [
                        'api_key' => $get('openrouter_api_key'),
                        'model' => $get('openrouter_model'),
                    ],
                    'ollama' => [
                        'base_url' => $get('ollama_base_url'),
                        'model' => $get('ollama_model'),
                    ],
                    'base_url' => $get('ollama_base_url'),
                    'system_prompt' => $get('ai_system_prompt'),
                    'temperature' => $get('ai_temperature'),
                    'tone' => $get('ai_tone'),
                ];
                $set($field, $ai->improveContent($state, $prompt ?? 'Improve this text.', $settings));
                Notification::make()->title(__('Content Improved'))->success()->send();
            });
    }

    protected static ?int $navigationSort = 1;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearCache')
                ->label(__('Clear System Cache'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Clear all cached data?'))
                ->modalDescription(__('This will force the website to re-fetch all data from the database. Use this if dashboard changes are not showing up on the frontend.'))
                ->action(function () {
                    \Illuminate\Support\Facades\Cache::flush();
                    Notification::make()
                        ->title(__('Cache Purged'))
                        ->body(__('Entire system cache has been successfully cleared.'))
                        ->success()
                        ->send();
                }),
            Action::make('save')
                ->label(__('Save All Configuration'))
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }

    public function save(): void
    {
        try {
            $state = $this->form->getState();
        } catch (ValidationException $exception) {
            Notification::make()
                ->title(__('Upload failed'))
                ->body(__('Please check the selected file type and size, then try again. Images must be valid PNG, JPG, WebP, SVG, or ICO files within the allowed size.'))
                ->danger()
                ->send();

            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title(__('Upload failed'))
                ->body($this->getUploadFailureMessage($exception))
                ->danger()
                ->send();

            return;
        }

        $translator = new AutoTranslateService();
        $autoTranslate = $state['auto_translate'] ?? true;

        // 1. Organization Profile
        $orgEn = [
            'company_name' => $state['company_name'],
            'website_title' => $state['website_title'] ?? '',
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
            'favicon' => $state['favicon'] ?? '',
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
            'values_list' => $this->normalizeCoreValues($state['values'] ?? []),
        ];

        $brandKm = $autoTranslate
            ? $translator->translateArray($brandEn, ['icon', 'image'], 'km')
            : (SystemSetting::get('brand_identity')['km'] ?? []);

        $brandKm['values_list'] = $this->syncCoreValueAssets(
            $brandEn['values_list'],
            $brandKm['values_list'] ?? []
        );

        SystemSetting::set('brand_identity', [
            'ceo_name' => $state['ceo_name'],
            'about_hero_image' => $state['about_hero_image'] ?? '',
            'about_section_images' => [
                $state['about_section_image_1'] ?? '',
                $state['about_section_image_2'] ?? '',
                $state['about_section_image_3'] ?? '',
                $state['about_section_image_4'] ?? '',
            ],
            'en' => $brandEn,
            'km' => $brandKm,
        ]);

        // 3. AI Settings (Multi-Provider)
        SystemSetting::set('ai_settings', [
            'provider' => $state['ai_provider'] ?? 'gemini',
            'gemini' => [
                'api_key' => $state['gemini_api_key'] ?? '',
                'model' => $state['gemini_model'] ?? 'gemini-3.1-flash-lite',
            ],
            'openrouter' => [
                'api_key' => $state['openrouter_api_key'] ?? '',
                'model' => $state['openrouter_model'] ?? 'deepseek/deepseek-chat-v3-0324:free',
            ],
            'ollama' => [
                'base_url' => $state['ollama_base_url'] ?? '',
                'model' => $state['ollama_model'] ?? '',
            ],
            'system_prompt' => $state['ai_system_prompt'] ?? '',
            'temperature' => $state['ai_temperature'] ?? 0.7,
            'tone' => $state['ai_tone'] ?? 'professional',
            'auto_translate' => $state['auto_translate'] ?? false,
        ]);

        // 4. Integration Settings
        SystemSetting::set('integration_settings', [
            'telegram_enabled' => (bool) $state['telegram_enabled'],
            'telegram_bot_token' => $state['telegram_bot_token'] ?? '',
            'telegram_jobs_chat_id' => $state['telegram_jobs_chat_id'] ?? '',
            'telegram_inquiries_chat_id' => $state['telegram_inquiries_chat_id'] ?? '',
        ]);
        // 5. Theme Settings
        SystemSetting::set('theme_settings', [
            'primary_color' => $state['primary_color'],
            'primary_color_hover' => $state['primary_color_hover'],
            'secondary_color' => $state['secondary_color'],
            'secondary_color_hover' => $state['secondary_color_hover'],
            'font_family_en' => $state['font_en'],
            'font_family_km' => $state['font_kh'],
            'footer_bg_color' => $state['footer_bg_color'] ?? '#071A33',
            'footer_accent_color' => $state['footer_accent_color'] ?? '#ED1C24',
            'news_page_bg_color' => $state['news_page_bg_color'] ?? '#F7F8FA',
        ]);

        // 6. Global Cache Purge (Force Frontend Sync)
        \Illuminate\Support\Facades\Cache::forget('global_settings_en');
        \Illuminate\Support\Facades\Cache::forget('global_settings_km');
        \Illuminate\Support\Facades\Cache::forget('system_setting_theme_settings');
        \Illuminate\Support\Facades\Cache::forget('system_setting_organization_profile');
        \Illuminate\Support\Facades\Cache::forget('system_setting_brand_identity');
        \Illuminate\Support\Facades\Cache::forget('system_setting_integration_settings');

        Notification::make()
            ->title(__('Global Configuration Saved'))
            ->body(__('All settings, including advanced AI persona and translations, have been updated.'))
            ->success()
            ->send();
    }

    protected function makeImageUpload(string $name, string $label, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->validationAttribute($label)
            ->image()
            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
            ->maxSize(5120)
            ->disk(config('filesystems.public_uploads_disk'))
            ->directory($directory)
            ->validationMessages([
                'uploaded' => __('The :attribute could not be uploaded. Please check your connection and storage settings, then try again.'),
                'image' => __('The :attribute must be a valid image file.'),
                'mimetypes' => __('The :attribute must be PNG, JPG, WebP, or SVG.'),
                'mimes' => __('The :attribute must be PNG, JPG, WebP, or SVG.'),
                'max' => __('The :attribute is too large. Please upload a smaller file.'),
            ]);
    }

    protected function getUploadFailureMessage(\Throwable $exception): string
    {
        $disk = config('filesystems.public_uploads_disk', 'public');

        if ($disk === 'r2') {
            return __('Cloudflare R2 could not store this file. Check R2 bucket name, endpoint, access key, secret key, and bucket permissions, then try again.');
        }

        return __('The server could not store this file. Check that storage is writable and that the public storage link exists, then try again.');
    }

    protected function normalizeCoreValues(array $values): array
    {
        return collect($values)
            ->map(function ($value) {
                if (is_string($value)) {
                    return [
                        'title' => $value,
                        'description' => '',
                        'icon' => 'lucide-shield',
                        'image' => null,
                    ];
                }

                if (!is_array($value)) {
                    return null;
                }

                return [
                    'title' => $value['title'] ?? ($value['value'] ?? ''),
                    'description' => $value['description'] ?? '',
                    'icon' => $value['icon'] ?? 'lucide-shield',
                    'image' => $value['image'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function syncCoreValueAssets(array $sourceValues, array $targetValues): array
    {
        $targetValues = $this->normalizeCoreValues($targetValues);

        foreach ($sourceValues as $index => $value) {
            if (!isset($targetValues[$index]) || !is_array($targetValues[$index])) {
                $targetValues[$index] = [
                    'title' => $value['title'] ?? '',
                    'description' => $value['description'] ?? '',
                ];
            }

            $targetValues[$index]['icon'] = $value['icon'] ?? 'lucide-shield';
            $targetValues[$index]['image'] = $value['image'] ?? null;
        }

        return array_values($targetValues);
    }
}
