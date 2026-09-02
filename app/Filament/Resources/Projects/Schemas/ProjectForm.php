<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Filament\Support\AIHelper;
use App\Filament\Support\OptimizedFileUpload;
use App\Filament\Support\TranslationHelper;
use App\Models\NewsArticle;
use App\Models\ProjectCategory;
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
use Filament\Schemas\Components\Grid;
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
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Example: Cambodia Gambling Management Commission Building'))
                                        ->required()
                                        ->live(onBlur: true)
                                        ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->label(__('Website Address'))
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Created automatically from the project name.'))
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
                                        ->getOptionLabelFromRecordUsing(fn (ProjectCategory $record): string => $record->localizedName())
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
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Shown in the project facts row.'))
                                        ->placeholder('Jan 2024 – Dec 2025'),
                                    TextInput::make('scale')
                                        ->label(__('Built Area / Scale'))
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Shown in the project facts row.'))
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
                                ->description(__('Write 2–4 short paragraphs explaining the project overview.'))
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
                                        ]),
                                ]),

                            Section::make(__('Architectural Concept & Background').' (ផ្ទៃរឿង និងទស្សនទានគម្រោង)')
                                ->description(__('Optional: Click to expand and add background context or design philosophy.'))
                                ->icon('heroicon-o-sparkles')
                                ->collapsible()
                                ->collapsed(fn ($record): bool => empty($record?->background) && empty($record?->designConcept))
                                ->schema([
                                    Grid::make(2)->schema([
                                        RichEditor::make('background')
                                            ->label(__('Project Background'))
                                            ->placeholder(__('Historical or contextual background of the project...'))
                                            ->toolbarButtons([['bold', 'italic', 'link'], ['bulletList', 'orderedList'], ['undo', 'redo']])
                                            ->hintActions([
                                                AIHelper::getImproveAction('background'),
                                            ])
                                            ->columnSpan(1),
                                        Textarea::make('designConcept')
                                            ->label(__('Design Concept'))
                                            ->placeholder(__('Key design philosophy, architectural aesthetic, and spatial planning principles...'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Use short paragraphs in plain text.'))
                                            ->rows(8)
                                            ->columnSpan(1),
                                    ]),
                                ]),

                            Section::make(__('Scope of Work & Objectives').' (វិសាលភាពការងារ និងគោលបំណង)')
                                ->description(__('Optional: Click to expand and add deliverables or key goals.'))
                                ->icon('heroicon-o-clipboard-document-check')
                                ->collapsible()
                                ->collapsed(fn ($record): bool => empty($record?->scopeContributions) && empty($record?->objectives))
                                ->schema([
                                    Grid::make(2)->schema([
                                        Textarea::make('scopeContributions')
                                            ->label(__('Scope of Work'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Write one contribution per line. Each line becomes a bullet item on the website.'))
                                            ->placeholder("Structural reinforced concrete framing\nMechanical, Electrical & Plumbing (MEP)\nArchitectural interior fit-out & finishing\nExternal facade & curtain wall glazing")
                                            ->rows(7)
                                            ->columnSpan(1),
                                        Textarea::make('objectives')
                                            ->label(__('Key Objectives'))
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Write one objective per line. Each line becomes a bullet item on the website.'))
                                            ->placeholder("Deliver project within specified timeline\nAchieve high engineering quality & energy efficiency\nZero lost-time safety incidents during construction")
                                            ->rows(7)
                                            ->columnSpan(1),
                                    ]),
                                ]),

                            Section::make(__('Engineering & Technical Notes').' (កំណត់ចំណាំបច្ចេកទេស និងវិស្វកម្ម)')
                                ->description(__('Optional: Click to expand and add engineering solutions or structural notes.'))
                                ->icon('heroicon-o-wrench-screwdriver')
                                ->collapsible()
                                ->collapsed(fn ($record): bool => empty($record?->engineeringNarrative))
                                ->schema([
                                    Textarea::make('engineeringNarrative')
                                        ->label(__('Engineering Notes'))
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Optional technical notes detailing engineering challenges and solutions.'))
                                        ->placeholder(__('Details about deep foundation excavation, post-tensioned slabs, BIM coordination, or specialized materials used...'))
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
                                        ->maxItems(50)
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
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Use only for your most important projects.')),
                                    Toggle::make('isActive')
                                        ->label(__('Publish This Project'))
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Visitors can see it only when this is on.'))
                                        ->default(true)
                                        ->required(),
                                ]),

                            Section::make(__('Related News'))
                                ->description(__('Optional: connect news articles about this project.'))
                                ->schema([
                                    Select::make('newsArticles')
                                        ->label(__('Related News Articles'))
                                        ->relationship('newsArticles', 'title')
                                        ->getOptionLabelFromRecordUsing(fn (NewsArticle $record): string => $record->getTranslation('title', app()->getLocale(), false) ?: ($record->getTranslation('title', 'en', false) ?: ''))
                                        ->multiple()
                                        ->searchable(),
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
