<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Filament\Support\AIHelper;
use App\Filament\Support\OptimizedFileUpload;
use App\Filament\Support\TranslationHelper;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make(__('1. Project Basics'))
                        ->description(__('Start with the information visitors see first.'))
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Section::make(__('Project Identity'))
                                ->description(__('Use the official project name and location.'))
                                ->columns(2)
                                ->schema([
                                    TextInput::make('title')
                                        ->label(__('Project Name'))
                                        ->helperText(__('Example: Cambodia Gambling Management Commission Building'))
                                        ->required()
                                        ->live(onBlur: true)
                                        ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->label(__('Website Address'))
                                        ->helperText(__('Created automatically from the project name.'))
                                        ->unique(ignoreRecord: true)
                                        ->required()
                                        ->disabled(fn ($get) => ! $get('_slug_manual'))
                                        ->dehydrated()
                                        ->suffixAction(
                                            Action::make('toggleSlugManual')
                                                ->icon(fn ($get) => $get('_slug_manual') ? 'heroicon-o-lock-open' : 'heroicon-o-pencil-square')
                                                ->tooltip(fn ($get) => $get('_slug_manual') ? __('Lock automatic address') : __('Edit address manually'))
                                                ->action(fn (Set $set, $get) => $set('_slug_manual', ! $get('_slug_manual'))),
                                        ),
                                    Hidden::make('_slug_manual')->default(false)->dehydrated(false),
                                    TextInput::make('location')
                                        ->label(__('Location'))
                                        ->placeholder(__('Example: Phnom Penh, Cambodia'))
                                        ->suffixAction(TranslationHelper::getAutoTranslateAction('location')),
                                    TextInput::make('client')
                                        ->label(__('Client / Owner'))
                                        ->placeholder(__('Example: Ministry of Economy and Finance')),
                                ]),

                            Section::make(__('Category & Timeline'))
                                ->columns(2)
                                ->schema([
                                    Select::make('project_category_id')
                                        ->label(__('Category'))
                                        ->relationship('projectCategory', 'name', fn ($query) => $query->orderBy('name->en'))
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                    Select::make('status')
                                        ->label(__('Project Status'))
                                        ->options(ProjectStatus::class)
                                        ->required()
                                        ->default(ProjectStatus::ONGOING),
                                    TextInput::make('timeline')
                                        ->label(__('Project Timeline'))
                                        ->helperText(__('Shown in the project facts row.'))
                                        ->placeholder('Jan 2024 – Dec 2025'),
                                    TextInput::make('scale')
                                        ->label(__('Built Area / Scale'))
                                        ->helperText(__('Shown in the project facts row.'))
                                        ->placeholder('8,087 m² · 17 floors'),
                                    DateTimePicker::make('completionDate')
                                        ->label(__('Completion Date')),
                                ]),
                        ]),

                    Step::make(__('2. Project Story'))
                        ->description(__('Keep it concise. Empty sections stay hidden on the website.'))
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make(__('Short Introduction'))
                                ->description(__('Write 2–4 short paragraphs explaining the project.'))
                                ->schema([
                                    RichEditor::make('description')
                                        ->label(__('Project Description'))
                                        ->required(fn (string $operation): bool => $operation === 'create')
                                        ->toolbarButtons([
                                            ['bold', 'italic', 'link'],
                                            [ToolbarButtonGroup::make('Heading', ['h2', 'h3'])->textualButtons()],
                                            ['bulletList', 'orderedList'],
                                            ['undo', 'redo'],
                                        ])
                                        ->hintActions([
                                            AIHelper::getGenerateAction('description', 'Project Description'),
                                            AIHelper::getImproveAction('description'),
                                            TranslationHelper::getAutoTranslateAction('description'),
                                        ]),
                                ]),

                            Section::make(__('Optional Detail Sections'))
                                ->description(__('Add only the sections that help visitors understand this project.'))
                                ->schema([
                                    RichEditor::make('background')
                                        ->label(__('Background'))
                                        ->toolbarButtons([['bold', 'italic', 'link'], ['bulletList', 'orderedList'], ['undo', 'redo']])
                                        ->hintActions([
                                            AIHelper::getImproveAction('background'),
                                            TranslationHelper::getAutoTranslateAction('background'),
                                        ]),
                                    Textarea::make('objectives')
                                        ->label(__('Objectives'))
                                        ->helperText(__('Write one objective per line. Each line becomes a bullet on the website.'))
                                        ->rows(5),
                                    Textarea::make('scopeContributions')
                                        ->label(__('Scope of Work'))
                                        ->helperText(__('Write one contribution per line. Do not use lists or HTML.'))
                                        ->rows(6),
                                    Textarea::make('designConcept')
                                        ->label(__('Design Concept'))
                                        ->helperText(__('Use short paragraphs in plain text.'))
                                        ->rows(5),
                                    Textarea::make('engineeringNarrative')
                                        ->label(__('Engineering Notes'))
                                        ->helperText(__('Optional technical challenges and solutions.'))
                                        ->rows(5),
                                ]),
                        ]),

                    Step::make(__('3. Photos'))
                        ->description(__('A strong cover image and a small gallery make the project credible.'))
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Section::make(__('Hero Image'))
                                ->description(__('Recommended: 1920 × 1080 WebP or JPG, under 1 MB.'))
                                ->schema([
                                    OptimizedFileUpload::hero('heroImage')
                                        ->directory('projects/hero')
                                        ->label(__('Main Project Image'))
                                        ->required(fn (string $operation): bool => $operation === 'create'),
                                ]),

                            Section::make(__('Photo Gallery'))
                                ->description(__('Add 4–8 strong images. Drag to choose the display order.'))
                                ->schema([
                                    Repeater::make('images')
                                        ->relationship('images')
                                        ->reorderable()
                                        ->orderColumn('sort_order')
                                        ->maxItems(15)
                                        ->schema([
                                            OptimizedFileUpload::image('url')
                                                ->directory('projects/gallery')
                                                ->label(__('Photo'))
                                                ->required(),
                                            TextInput::make('caption')
                                                ->label(__('Short Caption'))
                                                ->placeholder(__('Optional: describe this image')),
                                        ])
                                        ->columns(['default' => 2])
                                        ->itemLabel(fn (array $state): ?string => $state['caption'] ?? __('Gallery photo'))
                                        ->collapsible()
                                        ->grid(['default' => 2]),
                                ]),
                        ]),

                    Step::make(__('4. Review & Publish'))
                        ->description(__('Check the final settings before making this project visible.'))
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Section::make(__('Visibility'))
                                ->columns(2)
                                ->schema([
                                    Toggle::make('isFeatured')
                                        ->label(__('Show on Home Page'))
                                        ->helperText(__('Use only for your most important projects.')),
                                    Toggle::make('isActive')
                                        ->label(__('Publish This Project'))
                                        ->helperText(__('Visitors can see it only when this is on.'))
                                        ->default(true)
                                        ->required(),
                                ]),

                            Section::make(__('Related News'))
                                ->description(__('Optional: connect news articles about this project.'))
                                ->schema([
                                    Select::make('newsArticles')
                                        ->label(__('Related News Articles'))
                                        ->relationship('newsArticles', 'title')
                                        ->multiple()
                                        ->searchable()
                                        ->preload(),
                                ])
                                ->collapsible(),
                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString('project-step')
                    ->columnSpanFull(),
            ]);
    }
}
