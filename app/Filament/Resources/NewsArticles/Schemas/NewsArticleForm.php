<?php

namespace App\Filament\Resources\NewsArticles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use App\Filament\Support\OptimizedFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Support\TranslationHelper;
use App\Filament\Support\AIHelper;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('NewsEditor')
                    ->tabs([
                        // ─── TAB 1: CONTENT ───
                        Tab::make(__('Content'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('Article Identity'))
                                    ->columns(2)
                                    ->components([
                                        TextInput::make('title')
                                            ->label(__('Title'))
                                            ->required()
                                            ->live(onBlur: true)
                                            ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                                            ->hintAction(AIHelper::getImproveAction('title', 'Improve this news title to be more professional and catchy.'))
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                $set('slug', Str::slug($state));
                                                $set('metaTitle', $state);
                                            }),
                                        TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->helperText(__('Auto-generated from title. Click ✏️ to edit manually.'))
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->disabled(fn ($get) => !$get('_slug_manual'))
                                            ->dehydrated()
                                            ->suffixAction(
                                                \Filament\Actions\Action::make('toggleSlugManual')
                                                    ->icon(fn ($get) => $get('_slug_manual') ? 'heroicon-o-lock-open' : 'heroicon-o-pencil-square')
                                                    ->tooltip(fn ($get) => $get('_slug_manual') ? __('Lock (auto-generate)') : __('Edit manually'))
                                                    ->action(function (Set $set, $get) {
                                                        $set('_slug_manual', !$get('_slug_manual'));
                                                    })
                                            ),
                                        \Filament\Forms\Components\Hidden::make('_slug_manual')->default(false)->dehydrated(false),
                                    ]),

                                Section::make(__('Article Body'))
                                    ->components([
                                        Textarea::make('excerpt')
                                            ->label(__('Excerpt'))
                                            ->hintActions([
                                                AIHelper::getImproveAction('excerpt', 'Make this excerpt more engaging.'),
                                                TranslationHelper::getAutoTranslateAction('excerpt'),
                                            ])
                                            ->rows(2)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('metaDescription', $state)),

                                        RichEditor::make('content')->resizableImages()
                                            ->label(__('Content'))
                                            ->required()
                                            ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                            ->fileAttachmentsVisibility('public')
                                            ->fileAttachmentsDirectory('news/content')
                                            ->hintActions([
                                                AIHelper::getGenerateAction('content', 'News Article'),
                                                AIHelper::getImproveAction('content', 'Rewrite this news article to be more professional.'),
                                            ])
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, $state, $get) {
                                                if (!$get('excerpt')) {
                                                    $excerpt = Str::limit(strip_tags($state), 160);
                                                    $set('excerpt', $excerpt);
                                                    $set('metaDescription', $excerpt);
                                                }
                                                $wordCount = str_word_count(strip_tags($state));
                                                $set('readTime', (int) ceil($wordCount / 200));
                                            }),
                                    ]),
                            ]),

                        // ─── TAB 2: MEDIA ───
                        Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make(__('Cover Image'))
                                    ->components([
                                        OptimizedFileUpload::hero('coverImage')
                                            ->directory('news/covers')
                                            ->label(__('Cover Image')),
                                    ]),

                                Section::make(__('Gallery'))
                                    ->description(__('Upload additional images (max 12)'))
                                    ->collapsible()
                                    ->components([
                                        FileUpload::make('gallery')
                                            ->label(__('Gallery Images'))
                                            ->image()
                                            ->multiple()
                                            ->maxFiles(12)
                                            ->reorderable()
                                            ->disk(config('filesystems.public_uploads_disk'))
                                            ->visibility('public')
                                            ->directory('news/gallery')
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth('1920')
                                            ->imageResizeTargetHeight('1080')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->panelLayout('grid'),
                                    ]),

                                Section::make(__('Video'))
                                    ->collapsible()
                                    ->collapsed()
                                    ->components([
                                        TextInput::make('videoUrl')
                                            ->label(__('Video URL'))
                                            ->url()
                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                            ->helperText(__('Paste a YouTube or Vimeo link.')),
                                    ]),
                            ]),

                        // ─── TAB 3: PUBLISHING ───
                        Tab::make(__('Publishing'))
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Section::make(__('Schedule & Category'))
                                    ->columns(3)
                                    ->components([
                                        DateTimePicker::make('publishedAt')
                                            ->label(__('Published At'))
                                            ->required()
                                            ->default(now())
                                            ->native(false),
                                        TextInput::make('category')
                                            ->label(__('Category'))
                                            ->required()
                                            ->suffixAction(TranslationHelper::getAutoTranslateAction('category')),
                                        TextInput::make('readTime')
                                            ->label(__('Read Time'))
                                            ->suffix(__('mins'))
                                            ->numeric(),
                                    ]),

                                Section::make(__('Author'))
                                    ->columns(2)
                                    ->components([
                                        Select::make('authorId')
                                            ->label(__('Author'))
                                            ->relationship('author', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if ($state) {
                                                    $employee = \App\Models\Employee::find($state);
                                                    if ($employee) {
                                                        $set('authorName', $employee->name);
                                                    }
                                                }
                                            })
                                            ->default(auth()->user()?->employee?->id)
                                            ->afterStateHydrated(function ($state, Set $set, $get) {
                                                if (!$get('authorName') && $state) {
                                                    $employee = \App\Models\Employee::find($state);
                                                    if ($employee) {
                                                        $set('authorName', $employee->name);
                                                    }
                                                }
                                            }),
                                        TextInput::make('authorName')
                                            ->label(__('Author Name'))
                                            ->suffixAction(TranslationHelper::getAutoTranslateAction('authorName'))
                                            ->dehydrated()
                                            ->default(auth()->user()?->name),
                                    ]),

                                Section::make(__('Tags & Visibility'))
                                    ->columns(2)
                                    ->components([
                                        \Filament\Forms\Components\TagsInput::make('tags')
                                            ->label(__('Tags'))
                                            ->placeholder('news, update, announcement')
                                            ->columnSpanFull(),
                                        TextInput::make('year')
                                            ->label(__('Year'))
                                            ->numeric()
                                            ->default(date('Y')),
                                        Grid::make(3)->components([
                                            Toggle::make('isFeatured')
                                                ->label(__('Featured'))
                                                ->inline(false),
                                            Toggle::make('isTrending')
                                                ->label(__('Trending'))
                                                ->inline(false),
                                            Toggle::make('isActive')
                                                ->label(__('Active'))
                                                ->default(true)
                                                ->inline(false),
                                        ]),
                                    ]),

                                Section::make(__('SEO'))
                                    ->collapsed()
                                    ->description(__('Override auto-generated meta tags'))
                                    ->components([
                                        TextInput::make('metaTitle')
                                            ->label(__('Meta Title'))
                                            ->suffixAction(TranslationHelper::getAutoTranslateAction('metaTitle')),
                                        Textarea::make('metaDescription')
                                            ->label(__('Meta Description'))
                                            ->rows(2)
                                            ->hintAction(TranslationHelper::getAutoTranslateAction('metaDescription')),
                                    ]),

                                Section::make(__('Related Projects'))
                                    ->collapsed()
                                    ->description(__('Link this article to related projects'))
                                    ->components([
                                        Select::make('projects')
                                            ->label(__('Projects'))
                                            ->relationship('projects', 'title')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
