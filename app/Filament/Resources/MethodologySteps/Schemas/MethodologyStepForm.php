<?php

namespace App\Filament\Resources\MethodologySteps\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Support\TranslationHelper;

class MethodologyStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextInput::make('title')
                            ->required()
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('title')),
                        RichEditor::make('description')
                            ->required()
                            ->hintAction(TranslationHelper::getAutoTranslateAction('description'))
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->components([
                                TextInput::make('icon')
                                    ->required()
                                    ->default('lucide-settings')
                                    ->helperText('Use Lucide icon name (e.g., lucide-users)'),
                                TextInput::make('orderIndex')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('isActive')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ])
            ]);
    }
}
