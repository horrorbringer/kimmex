<?php

namespace App\Filament\Pages;

use App\Jobs\TranslateSystemSettings;
use App\Models\SystemSetting;
use App\Services\AIGeneratorService;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            // Organization English
            'company_name_en' => $org['en']['company_name'] ?? ($org['company_name'] ?? 'Kimmex Construction'),
            'website_title_en' => $org['en']['website_title'] ?? ($org['website_title'] ?? ''),
            'tagline_en' => $org['en']['tagline'] ?? ($org['tagline'] ?? ''),
            'working_hours_en' => $org['en']['working_hours'] ?? ($org['working_hours'] ?? ''),
            'address_en' => $org['en']['address'] ?? ($org['address'] ?? ''),

            // Organization Khmer
            'company_name_km' => $org['km']['company_name'] ?? '',
            'website_title_km' => $org['km']['website_title'] ?? '',
            'tagline_km' => $org['km']['tagline'] ?? '',
            'working_hours_km' => $org['km']['working_hours'] ?? '',
            'address_km' => $org['km']['address'] ?? '',

            // Organization General
            'company_name' => $org['en']['company_name'] ?? ($org['company_name'] ?? 'Kimmex Construction'),
            'tagline' => $org['en']['tagline'] ?? '',
            'registration_number' => $org['registration_number'] ?? '',
            'founded_date' => $org['founded_date'] ?? '',
            'phone' => $org['phone'] ?? '',
            'email' => $org['email'] ?? '',
            'working_hours' => $org['en']['working_hours'] ?? '',
            'address' => $org['en']['address'] ?? '',
            'google_maps_url' => $org['google_maps_url'] ?? '',
            'logo' => $org['logo'] ?? '',
            'logo_header' => $org['logo_header'] ?? '',
            'logo_footer' => $org['logo_footer'] ?? '',
            'favicon' => $org['favicon'] ?? '',
            'website_title' => $org['en']['website_title'] ?? '',

            // Social
            'facebook' => $org['facebook'] ?? '',
            'linkedin' => $org['linkedin'] ?? '',
            'youtube' => $org['youtube'] ?? '',
            'instagram' => $org['instagram'] ?? '',
            'telegram' => $org['telegram'] ?? '',
            'tiktok' => $org['tiktok'] ?? '',
            'career_telegram_channels' => SystemSetting::get('career_telegram_channels', []),

            // Branding English & Khmer
            'ceo_name' => $brand['ceo_name'] ?? '',
            'about_hero_image' => $brand['about_hero_image'] ?? '',
            'about_safety_image' => $brand['about_safety_image'] ?? '',
            'about_section_image_1' => $brand['about_section_images'][0] ?? '',
            'about_section_image_2' => $brand['about_section_images'][1] ?? '',
            'about_section_image_3' => $brand['about_section_images'][2] ?? '',
            'about_section_image_4' => $brand['about_section_images'][3] ?? '',
            'home_about_large_image' => $brand['home_about_large_image'] ?? '',
            'home_about_top_image' => $brand['home_about_top_image'] ?? '',
            'home_about_bottom_image' => $brand['home_about_bottom_image'] ?? '',
            'company_story_en' => $brand['en']['company_story'] ?? ($brand['company_story'] ?? ''),
            'company_story_km' => $brand['km']['company_story'] ?? '',
            'ceo_message_en' => $brand['en']['ceo_message'] ?? ($brand['ceo_message'] ?? ''),
            'ceo_message_km' => $brand['km']['ceo_message'] ?? '',
            'mission_en' => $brand['en']['mission'] ?? ($brand['mission'] ?? ''),
            'mission_km' => $brand['km']['mission'] ?? '',
            'vision_en' => $brand['en']['vision'] ?? ($brand['vision'] ?? ''),
            'vision_km' => $brand['km']['vision'] ?? '',
            'goal_en' => $brand['en']['goal'] ?? ($brand['goal'] ?? ''),
            'goal_km' => $brand['km']['goal'] ?? '',
            'company_story' => $brand['en']['company_story'] ?? ($brand['company_story'] ?? ''),
            'ceo_message' => $brand['en']['ceo_message'] ?? ($brand['ceo_message'] ?? ''),
            'mission' => $brand['en']['mission'] ?? ($brand['mission'] ?? ''),
            'vision' => $brand['en']['vision'] ?? ($brand['vision'] ?? ''),
            'goal' => $brand['en']['goal'] ?? ($brand['goal'] ?? ''),
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
        $this->availableModels = (new AIGeneratorService)->getAvailableModels(
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
        if (! empty($currentModel) && ! isset($this->availableModels[$currentModel])) {
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
                        // --- 1. COMPANY & BRAND TAB ---
                        Tab::make(__('Company & Brand'))
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Tabs::make('CompanyBrandSubTabs')
                                    ->tabs([
                                        // --- Sub-Tab 1: Profile, Contact & Logos ---
                                        Tab::make(__('General Profile & Contact'))
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    Section::make(__('Identity & Brand Assets'))
                                                        ->description(__('Manage logos, favicon, registration, and date.'))
                                                        ->columnSpan(1)
                                                        ->schema([
                                                            Grid::make(2)->schema([
                                                                $this->makeImageUpload('logo', __('Default Logo'), 'organization')
                                                                    ->helperText(__('Main logo. Used if header/footer logo is not set.')),
                                                                $this->makeImageUpload('favicon', __('Favicon'), 'organization')
                                                                    ->helperText(__('PNG, ICO, JPG, WebP, AVIF, or SVG. Maximum size: 2 MB.'))
                                                                    ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/jpeg', 'image/webp', 'image/avif', 'image/svg+xml'])
                                                                    ->mimeTypeMap(['webp' => 'image/webp', 'avif' => 'image/avif'])
                                                                    ->maxSize(2048),
                                                                $this->makeImageUpload('logo_header', __('Header Logo'), 'organization')
                                                                    ->helperText(__('Optional. Navbar header logo. Falls back to Default Logo.')),
                                                                $this->makeImageUpload('logo_footer', __('Footer Logo'), 'organization')
                                                                    ->helperText(__('Optional. Site footer logo. Falls back to Default Logo.')),
                                                            ]),
                                                            TextInput::make('registration_number')->label(__('Registration #')),
                                                            DatePicker::make('founded_date')->label(__('Founded Date')),
                                                        ]),

                                                    Section::make(__('Contact & Location'))
                                                        ->description(__('Global contact details and map.'))
                                                        ->columnSpan(1)
                                                        ->schema([
                                                            TextInput::make('email')->email()->label(__('Official Email')),
                                                            TextInput::make('phone')->tel()->label(__('Contact Phone')),
                                                            TextInput::make('google_maps_url')->url()->label(__('Google Maps Link')),
                                                        ]),
                                                ]),

                                                Section::make(__('Localized Organization Profile'))
                                                    ->description(__('Manage company name, tagline, address, and office hours for English and Khmer languages.'))
                                                    ->schema([
                                                        Tabs::make('OrgLocalizedTabs')
                                                            ->tabs([
                                                                Tab::make('🇬🇧 English')
                                                                    ->schema([
                                                                        TextInput::make('company_name_en')->label(__('Company Name (English)'))->required(),
                                                                        TextInput::make('website_title_en')
                                                                            ->label(__('Website Title (English)'))
                                                                            ->helperText(__('Custom title used for browser tabs and SEO.')),
                                                                        TextInput::make('tagline_en')
                                                                            ->label(__('Tagline (English)')),
                                                                        TextInput::make('working_hours_en')->label(__('Office Hours (English)')),
                                                                        Textarea::make('address_en')->rows(3)->label(__('Physical Address (English)')),
                                                                    ]),
                                                                Tab::make('🇰🇭 ភាសាខ្មែរ (Khmer)')
                                                                    ->schema([
                                                                        TextInput::make('company_name_km')->label(__('ឈ្មោះក្រុមហ៊ុន (ភាសាខ្មែរ)')),
                                                                        TextInput::make('website_title_km')->label(__('ចំណងជើងគេហទំព័រ (ភាសាខ្មែរ)')),
                                                                        TextInput::make('tagline_km')->label(__('ពាក្យស្លោក (ភាសាខ្មែរ)')),
                                                                        TextInput::make('working_hours_km')->label(__('ម៉ោងធ្វើការ (ភាសាខ្មែរ)')),
                                                                        Textarea::make('address_km')->rows(3)->label(__('អាសយដ្ឋាន (ភាសាខ្មែរ)')),
                                                                    ]),
                                                            ]),
                                                    ]),
                                            ]),

                                        // --- Sub-Tab 2: Story & Leadership ---
                                        Tab::make(__('Story & Leadership'))
                                            ->icon('heroicon-o-sparkles')
                                            ->schema([
                                                Section::make(__('Executive Message & Bio'))
                                                    ->schema([
                                                        TextInput::make('ceo_name')->label(__('CEO Name')),
                                                        Tabs::make('CeoStoryTabs')
                                                            ->tabs([
                                                                Tab::make('🇬🇧 English')
                                                                    ->schema([
                                                                        RichEditor::make('ceo_message_en')
                                                                            ->resizableImages()
                                                                            ->label(__('CEO Official Message (English)'))
                                                                            ->columnSpanFull()
                                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                                            ->fileAttachmentsVisibility('public'),
                                                                        Textarea::make('company_story_en')
                                                                            ->label(__('Our Story (English)'))
                                                                            ->rows(4),
                                                                    ]),
                                                                Tab::make('🇰🇭 ភាសាខ្មែរ (Khmer)')
                                                                    ->schema([
                                                                        RichEditor::make('ceo_message_km')
                                                                            ->resizableImages()
                                                                            ->label(__('សារផ្លូវការរបស់នាយកប្រតិបត្តិ (ភាសាខ្មែរ)'))
                                                                            ->columnSpanFull()
                                                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                                                            ->fileAttachmentsVisibility('public'),
                                                                        Textarea::make('company_story_km')
                                                                            ->label(__('រឿងរ៉ាវក្រុមហ៊ុន (ភាសាខ្មែរ)'))
                                                                            ->rows(4),
                                                                    ]),
                                                            ]),
                                                    ]),

                                                Section::make(__('Mission, Vision & Goals'))
                                                    ->schema([
                                                        Tabs::make('MissionTabs')
                                                            ->tabs([
                                                                Tab::make('🇬🇧 English')
                                                                    ->schema([
                                                                        Grid::make(3)->schema([
                                                                            Textarea::make('mission_en')->label(__('Mission (English)'))->rows(3),
                                                                            Textarea::make('vision_en')->label(__('Vision (English)'))->rows(3),
                                                                            Textarea::make('goal_en')->label(__('Goal (English)'))->rows(3),
                                                                        ]),
                                                                    ]),
                                                                Tab::make('🇰🇭 ភាសាខ្មែរ (Khmer)')
                                                                    ->schema([
                                                                        Grid::make(3)->schema([
                                                                            Textarea::make('mission_km')->label(__('បេសកកម្ម (ភាសាខ្មែរ)'))->rows(3),
                                                                            Textarea::make('vision_km')->label(__('ចក្ខុវិស័យ (ភាសាខ្មែរ)'))->rows(3),
                                                                            Textarea::make('goal_km')->label(__('គោលដៅ (ភាសាខ្មែរ)'))->rows(3),
                                                                        ]),
                                                                    ]),
                                                            ]),
                                                    ]),

                                                Section::make(__('Core Values'))
                                                    ->schema([
                                                        Repeater::make('values')
                                                            ->label(__('Core Values'))
                                                            ->schema([
                                                                FileUpload::make('image')
                                                                    ->label(__('Image'))
                                                                    ->image()
                                                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/avif', 'image/svg+xml'])
                                                                    ->mimeTypeMap(['webp' => 'image/webp', 'avif' => 'image/avif'])
                                                                    ->disk(config('filesystems.public_uploads_disk'))
                                                                    ->directory('brand/core-values')
                                                                    ->visibility('public')
                                                                    ->columnSpanFull(),
                                                            ])
                                                            ->reorderable()
                                                            ->collapsible()
                                                            ->itemLabel(fn (array $state): string => filled($state['image'] ?? null) ? __('Image') : __('New image')),
                                                    ]),
                                            ]),

                                        // --- Sub-Tab 3: Website Images ---
                                        Tab::make(__('Website Images'))
                                            ->icon('heroicon-o-photo')
                                            ->schema([
                                                Section::make(__('Home Page "About Us" Images'))
                                                    ->description(__('Manage the 3 showcase images displayed in the "About Us" section on the Home page.'))
                                                    ->columns(3)
                                                    ->schema([
                                                        $this->makeImageUpload('home_about_large_image', __('1. Large Left Image (Vertical 3:4)'), 'home/about')
                                                            ->helperText(__('Main vertical showcase image on the left side.'))
                                                            ->columnSpan(1),
                                                        $this->makeImageUpload('home_about_top_image', __('2. Top Right Image (Horizontal 4:3)'), 'home/about')
                                                            ->helperText(__('Top horizontal image on the right.'))
                                                            ->columnSpan(1),
                                                        $this->makeImageUpload('home_about_bottom_image', __('3. Bottom Right Image (Horizontal 4:3)'), 'home/about')
                                                            ->helperText(__('Bottom horizontal image on the right.'))
                                                            ->columnSpan(1),
                                                    ]),

                                                Section::make(__('About Page Images'))
                                                    ->description(__('Manage the About page hero image, Who We Are section images, and Quality & Safety standards image.'))
                                                    ->columns(2)
                                                    ->schema([
                                                        $this->makeImageUpload('about_hero_image', __('About Hero Image'), 'brand/about')
                                                            ->helperText(__('Used as the large background image at the top of the About page.'))
                                                            ->columnSpanFull(),
                                                        $this->makeImageUpload('about_section_image_1', __('Who We Are Image 1'), 'brand/about'),
                                                        $this->makeImageUpload('about_section_image_2', __('Who We Are Image 2'), 'brand/about'),
                                                        $this->makeImageUpload('about_section_image_3', __('Who We Are Image 3'), 'brand/about'),
                                                        $this->makeImageUpload('about_section_image_4', __('Who We Are Image 4'), 'brand/about'),
                                                        $this->makeImageUpload('about_safety_image', __('Quality & Safety Standards Image'), 'brand/about')
                                                            ->helperText(__('Used in the "OUR STANDARDS / Quality & Safety First" section.'))
                                                            ->columnSpanFull(),
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
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
                                                ->afterStateUpdated(function ($state, $get, $set, AIGeneratorService $ai) {
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
                                                            Action::make('fetchGeminiModels')
                                                                ->icon('heroicon-o-arrow-path')
                                                                ->action(function ($state, $get, AIGeneratorService $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels($state, 'gemini');
                                                                    Notification::make()->title(__('Gemini Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('gemini_model')
                                                        ->label(__('Active Model'))
                                                        ->options(fn () => $this->availableModels)->searchable(),
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
                                                            Action::make('fetchOpenRouterModels')
                                                                ->icon('heroicon-o-arrow-path')
                                                                ->action(function ($state, $get, AIGeneratorService $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels($state, 'openrouter');
                                                                    Notification::make()->title(__('OpenRouter Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('openrouter_model')
                                                        ->label(__('Active Model'))
                                                        ->options(fn () => $this->availableModels)->searchable(),
                                                ]),

                                            // Ollama Fields
                                            Group::make()
                                                ->visible(fn ($get) => $get('ai_provider') === 'ollama')
                                                ->schema([
                                                    TextInput::make('ollama_base_url')
                                                        ->label(__('Ollama Base URL'))
                                                        ->placeholder('http://localhost:11434')
                                                        ->hintAction(
                                                            Action::make('fetchOllamaModels')
                                                                ->icon('heroicon-o-arrow-path')
                                                                ->action(function ($state, $get, AIGeneratorService $ai) {
                                                                    $this->availableModels = $ai->getAvailableModels(null, 'ollama', $state);
                                                                    Notification::make()->title(__('Ollama Models Updated'))->success()->send();
                                                                })
                                                        ),
                                                    Select::make('ollama_model')
                                                        ->label(__('Active Model'))
                                                        ->options(fn () => $this->availableModels)->searchable(),
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
                                        Actions::make([
                                            Action::make('runTest')
                                                ->label(__('Generate Test Content'))
                                                ->icon('heroicon-m-play')
                                                ->action(function ($get, $set, AIGeneratorService $ai) {
                                                    $topic = $get('test_topic');
                                                    if (empty($topic)) {
                                                        return;
                                                    }
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
                                                        $set('test_result', $ai->generateContent($topic, $get('test_type') ?? 'article', null, $settings));
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
                                        TextInput::make('tiktok')
                                            ->label('TikTok')
                                            ->url()
                                            ->placeholder('https://www.tiktok.com/@your-account'),
                                    ]),
                                Section::make(__('Career Telegram Channels'))
                                    ->description(__('Manage reusable Telegram QR codes and links for job postings.'))
                                    ->schema([
                                        Repeater::make('career_telegram_channels')
                                            ->label(__('Career Telegram Channels'))
                                            ->schema([
                                                Hidden::make('id')->default(fn (): string => (string) Str::uuid()),
                                                TextInput::make('name')
                                                    ->label(__('Channel Name'))
                                                    ->required(),
                                                TextInput::make('url')
                                                    ->label(__('Telegram Careers Link'))
                                                    ->url()
                                                    ->required(),
                                                FileUpload::make('qr')
                                                    ->label(__('Telegram QR Image'))
                                                    ->image()
                                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/avif'])
                                                    ->mimeTypeMap(['webp' => 'image/webp', 'avif' => 'image/avif'])
                                                    ->maxSize(2048)
                                                    ->disk(config('filesystems.public_uploads_disk'))
                                                    ->directory('organization/career-telegram')
                                                    ->visibility('public')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): string => $state['name'] ?? __('New channel')),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
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
                                                        ->hint(fn ($get) => $get('jobs_chat_title') ? '✅ '.$get('jobs_chat_title') : null)
                                                        ->hintColor('success')
                                                        ->helperText(__('For job application alerts')),

                                                    TextInput::make('telegram_inquiries_chat_id')
                                                        ->label(__('Sales Chat ID (Inquiries)'))
                                                        ->placeholder('-100987654321')
                                                        ->hint(fn ($get) => $get('inquiries_chat_title') ? '✅ '.$get('inquiries_chat_title') : null)
                                                        ->hintColor('success')
                                                        ->helperText(__('For general inquiry alerts')),
                                                ]),

                                                Actions::make([
                                                    Action::make('testTelegram')
                                                        ->label(__('Verify & Test Connection'))
                                                        ->icon('heroicon-o-check-badge')
                                                        ->color('success')
                                                        ->action(function ($state, $get, $set) {
                                                            $token = $get('telegram_bot_token');
                                                            $targets = [
                                                                'jobs' => $get('telegram_jobs_chat_id'),
                                                                'inquiries' => $get('telegram_inquiries_chat_id'),
                                                            ];

                                                            if (! $token || (! array_filter($targets))) {
                                                                Notification::make()
                                                                    ->warning()
                                                                    ->title(__('Missing Config'))
                                                                    ->body(__('Please enter bot token and at least one chat ID.'))
                                                                    ->send();

                                                                return;
                                                            }

                                                            $successCount = 0;
                                                            foreach ($targets as $key => $chatId) {
                                                                if (! $chatId) {
                                                                    continue;
                                                                }
                                                                try {
                                                                    // 1. Get Chat Info (Title)
                                                                    $response = Http::get("https://api.telegram.org/bot{$token}/getChat", [
                                                                        'chat_id' => $chatId,
                                                                    ]);

                                                                    if ($response->successful()) {
                                                                        $chatData = $response->json('result');
                                                                        $title = $chatData['title'] ?? ($chatData['first_name'] ?? 'Private Chat');
                                                                        $set($key.'_chat_title', $title);

                                                                        // 2. Send Test Message
                                                                        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                                                                            'chat_id' => $chatId,
                                                                            'text' => "🚀 *KIMMEX VERIFIED*\nThis chat is now connected for ".strtoupper($key).' alerts!',
                                                                            'parse_mode' => 'Markdown',
                                                                        ]);
                                                                        $successCount++;
                                                                    }
                                                                } catch (\Exception $e) {
                                                                    Log::error("Telegram {$key} Verify Failed: ".$e->getMessage());
                                                                }
                                                            }

                                                            Notification::make()
                                                                ->success()
                                                                ->title(__('Verification Complete'))
                                                                ->body(__('Verified and sent test messages to :count chat(s).', ['count' => $successCount]))
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
                                        ColorPicker::make('primary_color')->label(__('Primary Accent'))->live(),
                                        ColorPicker::make('primary_color_hover')->label(__('Primary Accent Hover'))->live(),
                                        ColorPicker::make('secondary_color')->label(__('Secondary Color'))->live(),
                                        ColorPicker::make('secondary_color_hover')->label(__('Secondary Color Hover'))->live(),
                                    ]),
                                Section::make(__('Footer Appearance'))
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('footer_bg_color')->label(__('Footer Background Color'))->live(),
                                        ColorPicker::make('footer_accent_color')->label(__('Footer Accent/Link Color'))->live(),
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
                    ]),
            ])
            ->statePath('data');
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
                    Cache::flush();
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
                ->body(__('Please check the selected file type and size, then try again. Images must be valid PNG, JPG, WebP, AVIF, SVG, or ICO files within the allowed size.'))
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

        $autoTranslate = $state['auto_translate'] ?? true;

        // 1. Organization Profile
        $orgEn = [
            'company_name' => $state['company_name_en'] ?? ($state['company_name'] ?? 'Kimmex'),
            'website_title' => $state['website_title_en'] ?? ($state['website_title'] ?? ''),
            'tagline' => $state['tagline_en'] ?? ($state['tagline'] ?? ''),
            'address' => $state['address_en'] ?? ($state['address'] ?? ''),
            'working_hours' => $state['working_hours_en'] ?? ($state['working_hours'] ?? ''),
        ];

        $existingOrganizationProfile = SystemSetting::get('organization_profile', []);
        $existingOrgKm = $existingOrganizationProfile['km'] ?? [];

        $orgKmManual = array_filter([
            'company_name' => $state['company_name_km'] ?? null,
            'website_title' => $state['website_title_km'] ?? null,
            'tagline' => $state['tagline_km'] ?? null,
            'address' => $state['address_km'] ?? null,
            'working_hours' => $state['working_hours_km'] ?? null,
        ], fn ($val) => filled($val));

        $orgKm = array_merge($existingOrgKm, $orgKmManual);

        SystemSetting::set('organization_profile', [
            ...$existingOrganizationProfile,
            'registration_number' => $state['registration_number'],
            'founded_date' => $state['founded_date'],
            'phone' => $state['phone'],
            'email' => $state['email'],
            'google_maps_url' => $state['google_maps_url'],
            'logo' => $state['logo'],
            'logo_header' => $state['logo_header'] ?? '',
            'logo_footer' => $state['logo_footer'] ?? '',
            'favicon' => $state['favicon'] ?? '',
            'facebook' => $state['facebook'],
            'linkedin' => $state['linkedin'],
            'youtube' => $state['youtube'],
            'instagram' => $state['instagram'],
            'telegram' => $state['telegram'],
            'tiktok' => $state['tiktok'],
            'en' => $orgEn,
            'km' => $orgKm,
        ]);

        // 2. Brand Identity
        $brandEn = [
            'company_story' => $state['company_story_en'] ?? ($state['company_story'] ?? ''),
            'ceo_message' => $state['ceo_message_en'] ?? ($state['ceo_message'] ?? ''),
            'mission' => $state['mission_en'] ?? ($state['mission'] ?? ''),
            'vision' => $state['vision_en'] ?? ($state['vision'] ?? ''),
            'goal' => $state['goal_en'] ?? ($state['goal'] ?? ''),
            'values_list' => $this->normalizeCoreValues($state['values'] ?? []),
        ];

        $existingBrand = SystemSetting::get('brand_identity', []);
        $existingBrandKm = $existingBrand['km'] ?? [];

        $brandKmManual = array_filter([
            'company_story' => $state['company_story_km'] ?? null,
            'ceo_message' => $state['ceo_message_km'] ?? null,
            'mission' => $state['mission_km'] ?? null,
            'vision' => $state['vision_km'] ?? null,
            'goal' => $state['goal_km'] ?? null,
        ], fn ($val) => filled($val));

        $brandKm = array_merge($existingBrandKm, $brandKmManual);

        $brandKm['values_list'] = $this->syncCoreValueAssets(
            $brandEn['values_list'],
            $brandKm['values_list'] ?? []
        );

        SystemSetting::set('brand_identity', [
            'ceo_name' => $state['ceo_name'],
            'about_hero_image' => $state['about_hero_image'] ?? '',
            'about_safety_image' => $state['about_safety_image'] ?? '',
            'about_section_images' => [
                $state['about_section_image_1'] ?? '',
                $state['about_section_image_2'] ?? '',
                $state['about_section_image_3'] ?? '',
                $state['about_section_image_4'] ?? '',
            ],
            'home_about_large_image' => $state['home_about_large_image'] ?? '',
            'home_about_top_image' => $state['home_about_top_image'] ?? '',
            'home_about_bottom_image' => $state['home_about_bottom_image'] ?? '',
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

        SystemSetting::set('career_telegram_channels', collect($state['career_telegram_channels'] ?? [])
            ->filter(fn ($channel): bool => is_array($channel) && filled($channel['name'] ?? null))
            ->map(fn (array $channel): array => [
                'id' => filled($channel['id'] ?? null) ? $channel['id'] : (string) Str::uuid(),
                'name' => $channel['name'],
                'url' => $channel['url'] ?? '',
                'qr' => $channel['qr'] ?? '',
            ])
            ->values()
            ->all());
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

        if ($autoTranslate) {
            TranslateSystemSettings::dispatch($orgEn, $brandEn)->onQueue('default');
        }

        // 6. Global Cache Purge (Force Frontend Sync)
        Cache::forget('global_settings_en');
        Cache::forget('global_settings_km');
        Cache::forget('global_settings_kh');
        Cache::forget('system_setting_theme_settings');
        Cache::forget('system_setting_organization_profile');
        Cache::forget('system_setting_brand_identity');
        Cache::forget('system_setting_integration_settings');
        foreach (['en', 'km', 'kh'] as $locale) {
            Cache::forget("home_about_data_{$locale}");
        }

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
            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/avif', 'image/svg+xml'])
            ->mimeTypeMap(['webp' => 'image/webp', 'avif' => 'image/avif'])
            ->maxSize(5120)
            ->disk(config('filesystems.public_uploads_disk'))
            ->directory($directory)
            ->validationMessages([
                'uploaded' => __('The :attribute could not be uploaded. Please check your connection and storage settings, then try again.'),
                'image' => __('The :attribute must be a valid image file.'),
                'mimetypes' => __('The :attribute must be PNG, JPG, WebP, AVIF, or SVG.'),
                'mimes' => __('The :attribute must be PNG, JPG, WebP, AVIF, or SVG.'),
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

                if (! is_array($value)) {
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
            if (! isset($targetValues[$index]) || ! is_array($targetValues[$index])) {
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
