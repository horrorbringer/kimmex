<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Support\TranslationHelper;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('General Information'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required()
                                ->live(onBlur: true)
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('icon')
                                ->label(__('Icon'))
                                ->placeholder('e.g. heroicon-o-academic-cap'),
                        ]),
                    ]),

                Section::make(__('Content Details'))
                    ->components([
                        Grid::make(1)->components([
                             Textarea::make('summary')
                                ->label(__('Summary'))
                                ->hintAction(TranslationHelper::getAutoTranslateAction('summary'))
                                ->maxLength(1000),
                            RichEditor::make('description')
                                ->label(__('Description')),
                        ]),
                    ]),

                Section::make(__('Media & Features'))
                    ->components([
                        FileUpload::make('image')
                            ->label(__('Image'))
                            ->image()
                            ->directory('services')
                            ->columnSpanFull(),
                        
                        Repeater::make('features')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Settings'))
                    ->components([
                        Grid::make(2)->components([
                            Toggle::make('is_active')
                                ->label(__('Is Active'))
                                ->default(true),
                            TextInput::make('order_index')
                                ->label(__('Order'))
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),
            ]);
    }
}
