<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use App\Services\AutoTranslateService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ManageOrganization extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Organization');
    }

    public static function getNavigationSort(): ?int
    {
        return 0;
    }

    public function getTitle(): string
    {
        return __('Manage Organization Profile');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin();
    }

    protected string $view = 'filament.pages.manage-organization';

    public ?array $data = [];

    public function mount(): void
    {
        $allData = SystemSetting::get('organization_profile', []);

        $this->data = [
            'company_name' => $allData['en']['company_name'] ?? 'Kimmex Construction',
            'tagline' => $allData['en']['tagline'] ?? 'Cambodia\'s Premier Construction Partner',
            'registration_number' => $allData['registration_number'] ?? '',
            'founded_date' => $allData['founded_date'] ?? '',
            'address' => $allData['en']['address'] ?? '',
            'phone' => $allData['phone'] ?? '',
            'email' => $allData['email'] ?? '',
            'working_hours' => $allData['en']['working_hours'] ?? '',
            'google_maps_url' => $allData['google_maps_url'] ?? '',
            'logo' => $allData['logo'] ?? '',
            'facebook' => $allData['facebook'] ?? '',
            'linkedin' => $allData['linkedin'] ?? '',
            'youtube' => $allData['youtube'] ?? '',
            'instagram' => $allData['instagram'] ?? '',
            'telegram' => $allData['telegram'] ?? '',
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Section::make(__('Company Identity'))
                        ->description(__('Legal name and branding assets.'))
                        ->icon('heroicon-o-identification')
                        ->columnSpan(2)
                        ->schema([
                            FileUpload::make('logo')
                                ->label(__('Company Logo'))
                                ->image()
                                ->disk('public')
                                ->directory('organization')
                                ->visibility('public')
                                ->maxSize(1024),
                            Grid::make(2)->schema([
                                TextInput::make('company_name')
                                    ->label(__('Official Company Name'))
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('tagline')
                                    ->label(__('Company Tagline/Slogan'))
                                    ->placeholder(__('e.g. Cambodia\'s Premier Construction Partner'))
                                    ->columnSpanFull(),
                                TextInput::make('registration_number')
                                    ->label(__('Registration Number')),
                                DatePicker::make('founded_date')
                                    ->label(__('Founded Date')),
                            ]),
                        ]),

                    Section::make(__('Contact & Hours'))
                        ->description(__('Direct communication channels.'))
                        ->icon('heroicon-o-phone')
                        ->columnSpan(1)
                        ->schema([
                            TextInput::make('phone')
                                ->label(__('Primary Phone'))
                                ->tel(),
                            TextInput::make('email')
                                ->label(__('Official Email'))
                                ->email(),
                            TextInput::make('working_hours')
                                ->label(__('Working Hours'))
                                ->placeholder('Mon - Fri: 8:00 AM - 5:00 PM'),
                        ]),
                ]),

                Section::make(__('Physical Presence'))
                    ->description(__('Location details and mapping.'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)->schema([
                            Textarea::make('address')
                                ->label(__('Headquarters Address'))
                                ->rows(3),
                            TextInput::make('google_maps_url')
                                ->label(__('Google Maps Embed URL'))
                                ->helperText(__('IMPORTANT: Must be the EMBED link (starts with https://www.google.com/maps/embed...). Get it from Google Maps > Share > Embed a map > Copy ONLY the src URL.'))
                                ->placeholder('https://www.google.com/maps/embed?pb=...')
                                ->url(),
                        ]),
                    ]),

                Section::make(__('Social Media Presence'))
                    ->description(__('Connect with clients on various platforms.'))
                    ->icon('heroicon-o-share')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('facebook')->label('Facebook')->url()->prefixIcon('heroicon-o-link'),
                            TextInput::make('linkedin')->label('LinkedIn')->url()->prefixIcon('heroicon-o-link'),
                            TextInput::make('youtube')->label('YouTube')->url()->prefixIcon('heroicon-o-link'),
                            TextInput::make('instagram')->label('Instagram')->url()->prefixIcon('heroicon-o-link'),
                            TextInput::make('telegram')->label('Telegram')->url()->prefixIcon('heroicon-o-link'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save & Update Profile'))
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check-badge'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $translator = new AutoTranslateService();

        // English content (source)
        $enContent = [
            'company_name' => $state['company_name'] ?? '',
            'tagline' => $state['tagline'] ?? '',
            'address' => $state['address'] ?? '',
            'working_hours' => $state['working_hours'] ?? '',
        ];

        // Auto-translate to Khmer
        $kmContent = $translator->translateArray($enContent, [], 'km');

        // Store combined data
        $profileData = [
            'registration_number' => $state['registration_number'] ?? '',
            'founded_date' => $state['founded_date'] ?? '',
            'phone' => $state['phone'] ?? '',
            'email' => $state['email'] ?? '',
            'google_maps_url' => $state['google_maps_url'] ?? '',
            'logo' => $state['logo'] ?? '',
            'facebook' => $state['facebook'] ?? '',
            'linkedin' => $state['linkedin'] ?? '',
            'youtube' => $state['youtube'] ?? '',
            'instagram' => $state['instagram'] ?? '',
            'telegram' => $state['telegram'] ?? '',
            'en' => $enContent,
            'km' => $kmContent,
        ];

        SystemSetting::set('organization_profile', $profileData);

        Notification::make()
            ->title(__('Profile Saved Successfully'))
            ->body(__('Organization identity and contact details have been updated.'))
            ->success()
            ->send();
    }
}
