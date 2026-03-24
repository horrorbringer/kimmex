<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Department Identity'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('name')
                                ->label(__('Name'))
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->unique(ignoreRecord: true)
                                ->required(),
                        ]),
                    ]),

                Section::make(__('About Department'))
                    ->components([
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
