<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
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
                    ->description('Basic information and photo of the employee')
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('name')
                                ->label(__('Full Name'))
                                ->required(),
                            FileUpload::make('image')
                                ->image()
                                ->directory('employees')
                                ->label(__('Profile Photo')),
                        ]),
                    ]),

                Section::make(__('Contact & Location'))
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('email')
                                ->label(__('Email'))
                                ->email(),
                            TextInput::make('phone')
                                ->label(__('Phone'))
                                ->tel(),
                            TextInput::make('location')
                                ->label(__('Location')),
                        ]),
                    ]),

                Section::make(__('Profile & Role'))
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('role')
                                ->label(__('Role'))
                                ->required(),
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
                    ->collapsed()
                    ->components([
                        Select::make('user_id')
                            ->label(__('Linked Admin User'))
                            ->relationship('user', 'email')
                            ->searchable()
                            ->placeholder('Select an admin user to link...')
                            ->helperText('Linking a user allows automatic author assignment for news articles.'),
                    ]),
            ]);
    }
}
