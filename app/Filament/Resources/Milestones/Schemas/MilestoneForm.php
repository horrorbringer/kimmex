<?php

namespace App\Filament\Resources\Milestones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Milestone Identity'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('year')
                                ->label(__('Year'))
                                ->placeholder('e.g., 2024')
                                ->required(),
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required(),
                        ]),
                    ]),

                Section::make(__('Content'))
                    ->components([
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Visual & Order'))
                    ->components([
                        Grid::make(2)->components([
                            FileUpload::make('image')
                                ->label(__('Image'))
                                ->image()
                                ->disk('public')
                                ->directory('milestones')
                                ->visibility('public'),
                            TextInput::make('sortOrder')
                                ->label(__('Order'))
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),
            ]);
    }
}
