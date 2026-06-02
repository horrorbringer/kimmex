<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Support\TranslationHelper;
use App\Filament\Support\AIHelper;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('ProjectEditor')
                    ->tabs([
                        Tab::make(__('Content'))
                            ->schema([
                                Section::make(__('Project Identity'))
                                    ->description(__('Main identity and URL settings'))
                                    ->columns(2)
                                    ->components([
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
                                        TextInput::make('location')
                                            ->label(__('Location'))
                                            ->suffixAction(TranslationHelper::getAutoTranslateAction('location')),
                                        TextInput::make('client')
                                            ->label(__('Client')),
                                    ]),

                                Section::make(__('Brief Description'))
                                    ->description(__('Short public summary used near the top of the project details page.'))
                                    ->components([
                                        RichEditor::make('description')
                                            ->label(__('Description'))
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsVisibility('public')
                                            ->hintActions([
                                                AIHelper::getGenerateAction('description', 'Project Description'),
                                                AIHelper::getImproveAction('description'),
                                            ])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('Project Overview'))
                                    ->description(__('Optional public narrative fields. Empty fields are hidden on the frontend.'))
                                    ->columns(2)
                                    ->components([
                                        TextInput::make('timeline')
                                            ->label(__('Timeline'))
                                            ->placeholder('e.g., Jan 2024 - Dec 2025'),
                                        TextInput::make('scale')
                                            ->label(__('Scale'))
                                            ->placeholder('e.g., 50,000 sqm or 5-story building'),
                                        RichEditor::make('background')
                                            ->label(__('Project Background'))
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsVisibility('public')
                                            ->hintActions([
                                                AIHelper::getImproveAction('background'),
                                                TranslationHelper::getAutoTranslateAction('background'),
                                            ])
                                            ->columnSpanFull(),
                                        RichEditor::make('objectives')
                                            ->label(__('Project Objectives'))
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsVisibility('public')
                                            ->hintActions([
                                                AIHelper::getImproveAction('objectives'),
                                                TranslationHelper::getAutoTranslateAction('objectives'),
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Section::make(__('Engineering & Design'))
                                    ->description(__('Optional technical details. Empty fields are hidden on the frontend.'))
                                    ->components([
                                        RichEditor::make('designConcept')
                                            ->label(__('Design Concept & Functions'))
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsVisibility('public'),
                                        RichEditor::make('scopeContributions')
                                            ->label(__('Specific Kimmex Contributions'))
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsVisibility('public'),
                                        RichEditor::make('engineeringNarrative')
                                            ->label(__('Challenges & Solutions (Engineering Narrative)'))
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsVisibility('public'),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ]),

                        Tab::make(__('Media'))
                            ->schema([
                                Section::make(__('Visual Assets'))
                                    ->columns(2)
                                    ->components([
                                        FileUpload::make('heroImage')
                                            ->image()
                                            ->disk(config('filesystems.public_uploads_disk'))
                                            ->visibility('public')
                                            ->directory('projects/hero')
                                            ->label(__('Hero Image'))
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('Project Gallery'))
                                    ->description(__('Additional project photographs and captions'))
                                    ->components([
                                        \Filament\Forms\Components\Repeater::make('images')
                                            ->relationship('images')
                                            ->reorderable('sort_order')
                                            ->orderColumn('sort_order')
                                            ->schema([
                                                FileUpload::make('url')
                                                    ->image()
                                                    ->disk(config('filesystems.public_uploads_disk'))
                                                    ->visibility('public')
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
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make(__('Publishing'))
                            ->schema([
                                Section::make(__('Categorization & Status'))
                                    ->columns(3)
                                    ->components([
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

                                Section::make(__('Settings'))
                                    ->columns(2)
                                    ->components([
                                        Toggle::make('isFeatured')
                                            ->label(__('Is Featured'))
                                            ->required(),
                                        Toggle::make('isActive')
                                            ->label(__('Is Active'))
                                            ->default(true)
                                            ->required(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
