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
    protected string $view = 'filament.pages.manage-branding';

    public ?array $data = [];

    public function mount(): void
    {
        $allData = SystemSetting::get('brand_identity', []);

        // Load the English (source) content for editing
        $this->data = [
            'ceo_name' => $allData['ceo_name'] ?? '',
            'ceo_message' => $allData['en']['ceo_message'] ?? '',
            'mission' => $allData['en']['mission'] ?? '',
            'vision' => $allData['en']['vision'] ?? '',
            'values' => $allData['en']['values'] ?? '',
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('CEO Identity'))
                    ->schema([
                        TextInput::make('ceo_name')
                            ->label(__('CEO Full Name'))
                            ->required(),
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
                        Textarea::make('values')
                            ->label(__('Core Values'))
                            ->rows(4),
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
            'ceo_message' => $state['ceo_message'] ?? '',
            'mission' => $state['mission'] ?? '',
            'vision' => $state['vision'] ?? '',
            'values' => $state['values'] ?? '',
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
