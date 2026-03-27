<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Support\TranslationHelper;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Project Identity'))
                    ->description(__('Main identity and URL settings'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required()
                                ->live(onBlur: true)
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->unique(ignoreRecord: true)
                                ->required(),
                        ]),
                        Grid::make(2)->components([
                            TextInput::make('location')
                                ->label(__('Location'))
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('location')),
                            TextInput::make('client')
                                ->label(__('Client')),
                        ]),
                    ]),

                Section::make(__('Project Overview'))
                    ->description(__('Detailed narratives and scale'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('timeline')
                                ->label(__('Timeline'))
                                ->placeholder('e.g., Jan 2024 - Dec 2025'),
                            TextInput::make('scale')
                                ->label(__('Scale'))
                                ->placeholder('e.g., 50,000 sqm or 5-story building'),
                        ]),
                        Textarea::make('background')
                            ->label(__('Project Background'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('background'))
                            ->rows(3),
                        Textarea::make('objectives')
                            ->label(__('Project Objectives'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('objectives'))
                            ->rows(3),
                    ]),

                Section::make(__('Engineering & Design'))
                    ->description(__('Technical details and solutions'))
                    ->components([
                        RichEditor::make('designConcept')
                            ->label(__('Design Concept & Functions')),
                        RichEditor::make('scopeContributions')
                            ->label(__('Specific Kimmex Contributions')),
                        RichEditor::make('engineeringNarrative')
                            ->label(__('Challenges & Solutions (Engineering Narrative)')),
                    ]),

                Section::make(__('Brief Description'))
                    ->components([
                        RichEditor::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Categorization & Status'))
                    ->components([
                        Grid::make(3)->components([
                            Select::make('project_category_id')
                                ->label(__('Category'))
                                ->relationship('projectCategory', 'name', fn($query) => $query->orderBy('name->en'))
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('status')
                                ->label(__('Status'))
                                ->options(\App\Enums\ProjectStatus::class)
                                ->required()
                                ->default(\App\Enums\ProjectStatus::ONGOING),
                            DateTimePicker::make('completionDate')
                                ->label(__('Completion Date')),
                        ]),
                    ]),

                Section::make(__('Project Gallery'))
                    ->description(__('Additional project photographs and captions'))
                    ->components([
                        \Filament\Forms\Components\Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                FileUpload::make('url')
                                    ->image()
                                    ->directory('projects/gallery')
                                    ->label(__('Photo'))
                                    ->required(),
                                TextInput::make('caption')
                                    ->label(__('Caption'))
                                    ->suffixAction(TranslationHelper::getAutoTranslateAction('caption'))
                                    ->placeholder(__('Enter a short caption...')),
                            ])
                            ->columns(['default' => 2])
                            ->itemLabel(fn(array $state): ?string => $state['caption'] ?? null)
                            ->collapsible()
                            ->grid(['default' => 2])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Visual Assets'))
                    ->components([
                        FileUpload::make('heroImage')
                            ->image()
                            ->directory('projects/hero')
                            ->label(__('Hero Image'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Settings'))
                    ->components([
                        Toggle::make('isFeatured')
                            ->label(__('Is Featured'))
                            ->required(),
                    ]),
            ]);
    }
}
