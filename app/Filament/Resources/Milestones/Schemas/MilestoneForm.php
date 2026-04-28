<?php

namespace App\Filament\Resources\Milestones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use App\Filament\Support\TranslationHelper;
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
                                ->required()
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('title')),
                        ]),
                    ]),

                Section::make(__('Content'))
                    ->components([
                        RichEditor::make('description')
                            ->label(__('Description'))
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->toolbarButtons([
                                'bold', 'italic', 'bulletList', 'orderedList', 'link', 'redo', 'undo'
                            ])
                            ->hintAction(TranslationHelper::getAutoTranslateAction('description'))
                            ->columnSpanFull(),
                        RichEditor::make('detailed_description')
                            ->label(__('Detailed Description (Shown on Click)'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('detailed_description'))
                            ->placeholder(__('Expanded narrative about this milestone...'))
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
                            Toggle::make('isActive')
                                ->label(__('Is Active'))
                                ->default(true)
                                ->required(),
                        ]),
                    ]),
            ]);
    }
}
