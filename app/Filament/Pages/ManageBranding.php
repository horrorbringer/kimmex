<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use App\Services\AutoTranslateService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;

class ManageBranding extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-sparkles';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Communication');
    }

    public function getTitle(): string
    {
        return __('Manage Brand Identity');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin();
    }

    protected string $view = 'filament.pages.manage-branding';

    public ?array $data = [];

    public function mount(): void
    {
        $allData = SystemSetting::get('brand_identity', []);

        // Load the English (source) content for editing
        $this->data = [
            'company_story' => $allData['en']['company_story'] ?? '',
            'ceo_name' => $allData['ceo_name'] ?? '',
            'ceo_message' => $allData['en']['ceo_message'] ?? '',
            'mission' => $allData['en']['mission'] ?? '',
            'vision' => $allData['en']['vision'] ?? '',
            'goal' => $allData['en']['goal'] ?? '',
            'values' => $allData['en']['values_list'] ?? [],
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('Company Identity'))
                    ->schema([
                        TextInput::make('ceo_name')
                            ->label(__('CEO Full Name'))
                            ->required(),
                        Textarea::make('company_story')
                            ->label(__('Company Story / About Us Text'))
                            ->rows(5)
                            ->helperText(__('A longer narrative about your company history and growth.')),
                        RichEditor::make('ceo_message')
                            ->label(__('CEO Message'))
                            ->helperText(__('Write in English — Khmer translation is generated automatically on save.'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Mission & Vision'))
                    ->description(__('Write in English — other languages are auto-translated when you save.'))
                    ->schema([
                        Textarea::make('mission')
                            ->label(__('Mission Statement'))
                            ->rows(3),
                        Textarea::make('vision')
                            ->label(__('Vision Statement'))
                            ->rows(3),
                        Textarea::make('goal')
                            ->label(__('Goal Statement'))
                            ->rows(3),
                    ]),

                Section::make(__('Core Values'))
                    ->description(__('Add your company core values as a list.'))
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('values')
                            ->label(__('Values List'))
                            ->schema([
                                TextInput::make('title')->label(__('Title'))->required(),
                                Textarea::make('description')->label(__('Description'))->rows(2),
                                \Filament\Forms\Components\Select::make('icon')
                                    ->label(__('Icon'))
                                    ->options([
                                        'lucide-shield' => 'Shield/Integrity',
                                        'lucide-award' => 'Award/Excellence',
                                        'lucide-handshake' => 'Handshake/Partnership',
                                        'lucide-lightbulb' => 'Lightbulb/Innovation',
                                        'lucide-heart' => 'Heart/Safety',
                                        'lucide-trending-up' => 'Trending/Growth',
                                    ])->searchable(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? null)
                            ->collapsible(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save & Auto-Translate'))
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-language'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $translator = new AutoTranslateService();

        // English content (source)
        $enContent = [
            'company_story' => $state['company_story'] ?? '',
            'ceo_message' => $state['ceo_message'] ?? '',
            'mission' => $state['mission'] ?? '',
            'vision' => $state['vision'] ?? '',
            'goal' => $state['goal'] ?? '',
            'values_list' => $state['values'] ?? [],
        ];

        // Auto-translate to Khmer
        $kmContent = $translator->translateArray($enContent, [], 'km');

        // Store everything
        $brandData = [
            'ceo_name' => $state['ceo_name'] ?? '',
            'en' => $enContent,
            'km' => $kmContent,
        ];

        SystemSetting::set('brand_identity', $brandData);

        Notification::make()
            ->title('Saved & Translated!')
            ->body('English content saved. Khmer translation generated automatically.')
            ->success()
            ->send();
    }
}
