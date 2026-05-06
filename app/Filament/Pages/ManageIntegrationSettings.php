<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;

class ManageIntegrationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-paper-airplane';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public function getTitle(): string
    {
        return __('Integrations & Notifications');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.manage-integration-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SystemSetting::get('integration_settings', []);

        $this->data = [
            'telegram_enabled' => $settings['telegram_enabled'] ?? false,
            'telegram_bot_token' => $settings['telegram_bot_token'] ?? '',
            'telegram_chat_id' => $settings['telegram_chat_id'] ?? '',
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('Telegram Notifications'))
                    ->description(__('Configure Telegram to receive automatic alerts when a user submits a contact form on the frontend.'))
                    ->schema([
                        Toggle::make('telegram_enabled')
                            ->label(__('Enable Telegram Alerts'))
                            ->default(false),
                            
                        TextInput::make('telegram_bot_token')
                            ->label(__('Bot Token'))
                            ->password()
                            ->revealable()
                            ->helperText(__('Get this from @BotFather on Telegram.'))
                            ->required(fn (\Filament\Forms\Get $get) => $get('telegram_enabled')),

                        TextInput::make('telegram_chat_id')
                            ->label(__('Chat ID / Channel ID'))
                            ->helperText(__('The Chat ID where messages should be sent. Start with -100 for channels.'))
                            ->required(fn (\Filament\Forms\Get $get) => $get('telegram_enabled')),
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

        SystemSetting::set('integration_settings', $state);

        Notification::make()
            ->title('Integration Settings Saved')
            ->success()
            ->send();
    }
}
