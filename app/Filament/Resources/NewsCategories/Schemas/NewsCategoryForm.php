<?php

namespace App\Filament\Resources\NewsCategories\Schemas;

use App\Filament\Support\TranslationHelper;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Category Details'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('name')
                                ->label(__('Category Name'))
                                ->placeholder('e.g., Building Construction')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('name')),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->placeholder('e.g., building-construction')
                                ->required()
                                ->unique(ignoreRecord: true),
                        ]),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->hintAction(TranslationHelper::getAutoTranslateAction('description')),
                    ]),

                Section::make(__('Display & Status'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('order_index')
                                ->label(__('Display Order'))
                                ->numeric()
                                ->default(0)
                                ->required(),
                            Toggle::make('is_active')
                                ->label(__('Active Status'))
                                ->default(true)
                                ->required(),
                        ]),
                    ]),
            ]);
    }
}
