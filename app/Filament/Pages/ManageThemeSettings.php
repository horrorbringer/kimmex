<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;

class ManageThemeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-paint-brush';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public function getTitle(): string
    {
        return __('Theme & Styling');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.manage-theme-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SystemSetting::get('theme_settings', []);

        $this->data = [
            'font_family_en' => $settings['font_family_en'] ?? 'Inter',
            'font_family_km' => $settings['font_family_km'] ?? 'Kantumruy Pro',
            'primary_color' => $settings['primary_color'] ?? '#dc2626', // titan-red
            'secondary_color' => $settings['secondary_color'] ?? '#0f172a', // titan-dark
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('Typography'))
                    ->description(__('Select the fonts used across the website for different languages.'))
                    ->schema([
                        Select::make('font_family_en')
                            ->label(__('English Font'))
                            ->options([
                                'Inter' => 'Inter',
                                'Roboto' => 'Roboto',
                                'Poppins' => 'Poppins',
                                'Montserrat' => 'Montserrat',
                                'Open Sans' => 'Open Sans',
                            ])
                            ->required(),

                        Select::make('font_family_km')
                            ->label(__('Khmer Font'))
                            ->options([
                                'Kantumruy Pro' => 'Kantumruy Pro',
                                'Battambang' => 'Battambang',
                                'Khmer OS Siemreap' => 'Khmer OS Siemreap',
                                'Suwannaphum' => 'Suwannaphum',
                                'Moul' => 'Moul',
                            ])
                            ->required(),
                    ])->columns(2),

                Section::make(__('Colors'))
                    ->description(__('Change the primary branding colors used on the website.'))
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label(__('Primary Color (Red / Accent)'))
                            ->required(),
                        ColorPicker::make('secondary_color')
                            ->label(__('Secondary Color (Dark / Background)'))
                            ->required(),
                    ])->columns(2),
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

        SystemSetting::set('theme_settings', $state);

        Notification::make()
            ->title('Theme Settings Saved')
            ->success()
            ->send();
    }
}
