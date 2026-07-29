<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Personnel Identity'))
                    ->icon('heroicon-o-identification')
                    ->description(__('Start here. Only the employee name and job title are required.'))
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('name')
                                ->label(__('Full Name'))
                                ->placeholder(__('E.g., Sok Dara'))
                                ->required()
                                ->columnSpan(2),
                            TextInput::make('role')
                                ->label(__('Job Title'))
                                ->placeholder(__('E.g., Project Manager'))
                                ->required(),
                            FileUpload::make('image')
                                ->image()
                                ->disk(config('filesystems.public_uploads_disk'))
                                ->directory('employees')
                                ->visibility('public')
                                ->label(__('Profile Photo'))
                                ->helperText(__('Optional. A clear head-and-shoulders photo works best.')),
                            Toggle::make('isActive')
                                ->label(__('Show on organization chart'))
                                ->helperText(__('Turn this off to hide the employee from public organization displays.'))
                                ->default(true)
                                ->hiddenOn('create')
                                ->required(),
                        ]),
                    ]),

                Section::make(__('Contact & Location'))
                    ->icon('heroicon-o-phone')
                    ->description(__('Optional contact details for the employee profile.'))
                    ->hiddenOn('create')
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('email')
                                ->label(__('Email'))
                                ->email()
                                ->placeholder('name@company.com'),
                            TextInput::make('phone')
                                ->label(__('Phone'))
                                ->tel()
                                ->placeholder('+855 ...'),
                            TextInput::make('location')
                                ->label(__('Location'))
                                ->placeholder(__('E.g., Phnom Penh')),
                        ]),
                    ]),

                Section::make(__('Profile & Role'))
                    ->icon('heroicon-o-briefcase')
                    ->description(__('Add only the details that help visitors understand this person’s work.'))
                    ->hiddenOn('create')
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('specialization')
                                ->label(__('Specialization')),
                            TextInput::make('experience')
                                ->label(__('Experience'))
                                ->placeholder('e.g. 5 Years'),
                        ]),
                        Textarea::make('bio')
                            ->label(__('Bio'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('System Integration'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->description(__('Optional. Use this only when the employee also has an admin account.'))
                    ->collapsed()
                    ->hiddenOn('create')
                    ->components([
                        Select::make('user_id')
                            ->label(__('Linked Admin User'))
                            ->relationship('user', 'email')
                            ->searchable()
                            ->placeholder(__('No admin account linked'))
                            ->helperText(__('Linking a user allows automatic author assignment for news articles.')),
                    ]),
            ]);
    }
}
