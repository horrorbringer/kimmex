<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Account'))
                    ->description(__('Login details for dashboard access.'))
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('Email address'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->helperText(__('Leave blank when editing to keep the current password.')),
                        TextInput::make('password_confirmation')
                            ->label(__('Confirm password'))
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->requiredWith('password')
                            ->dehydrated(false),
                        DateTimePicker::make('email_verified_at')
                            ->label(__('Email Verified At')),
                    ]),

                Section::make(__('Access & Profile'))
                    ->description(__('Control dashboard permission and profile image.'))
                    ->columns(2)
                    ->components([
                        Select::make('role')
                            ->label(__('Role'))
                            ->options([
                                'ADMIN' => __('Admin'),
                                'EDITOR' => __('Editor'),
                            ])
                            ->helperText(__('Admin can manage all dashboard modules. Editor has limited dashboard access.'))
                            ->default('EDITOR')
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__('Can Access Dashboard'))
                            ->helperText(__('Turn off to block this user from logging into the admin dashboard.'))
                            ->default(true)
                            ->disabled(fn ($record): bool => $record?->id === auth()->id())
                            ->dehydrated(fn ($record): bool => $record?->id !== auth()->id()),
                        FileUpload::make('image')
                            ->label(__('Profile Image'))
                            ->image()
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->directory('users/avatars')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
