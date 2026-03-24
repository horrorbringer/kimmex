<?php

namespace App\Filament\Resources\Policies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class PolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Policy Identity'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->unique(ignoreRecord: true)
                                ->required(),
                            TextInput::make('icon')
                                ->label(__('Icon'))
                                ->placeholder('heroicon-o-shield-check')
                                ->helperText(__('Heroicon name (e.g., heroicon-o-shield-check)')),
                            TextInput::make('sort_order')
                                ->label(__('Order'))
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),

                Section::make(__('Policy Body'))
                    ->components([
                        RichEditor::make('content')
                            ->label(__('Content'))
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make(__('Visibility'))
                    ->components([
                        Toggle::make('is_public')
                            ->label(__('Visible on Website'))
                            ->default(true),
                    ]),
            ]);
    }
}
