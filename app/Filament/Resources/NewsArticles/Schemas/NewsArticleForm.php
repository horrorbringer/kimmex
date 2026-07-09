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
            ->columns(1)
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
                    ->helperText(__('Auto-generated from title. Used for the article URL.'))
                    ->unique(ignoreRecord: true)
                    ->required(),

                Textarea::make('excerpt')
                    ->label(__('Excerpt'))
                    ->hintActions([
                        AIHelper::getImproveAction('excerpt', 'Make this article excerpt more engaging.'),
                        TranslationHelper::getAutoTranslateAction('excerpt'),
                    ])
                    ->rows(3)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('metaDescription', $state)),

                OptimizedFileUpload::hero('coverImage')
                    ->directory('news/covers')
                    ->label(__('Cover Image')),

                RichEditor::make('content')
                    ->label(__('Content'))
                    ->required()
                    ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsDirectory('news/content')
                    ->hintActions([
                        AIHelper::getGenerateAction('content', 'News Article'),
                        AIHelper::getImproveAction('content', 'Rewrite this news article to be more professional and well-structured.'),
                    ])
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state, $get) {
                        // Auto-generate excerpt if empty
                        if (!$get('excerpt')) {
                            $excerpt = Str::limit(strip_tags($state), 160);
                            $set('excerpt', $excerpt);
                            $set('metaDescription', $excerpt);
                        }
                        
                        // Auto-calculate read time (roughly 200 words per minute)
                        $wordCount = str_word_count(strip_tags($state));
                        $readTime = ceil($wordCount / 200);
                        $set('readTime', $readTime);
                    }),

                FileUpload::make('gallery')
                    ->label(__('Gallery'))
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
                    ->panelLayout('grid')
                    ->helperText(__('Upload multiple images for the article gallery (max 12, auto-optimized)')),

                TextInput::make('videoUrl')
                    ->label(__('Video URL (YouTube/Vimeo)'))
                    ->url()
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->helperText(__('Paste a YouTube or Vimeo link to embed a video in this article.')),

                Section::make(__('Publishing Info'))
                    ->icon('heroicon-o-calendar')
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
                            ->suffix(__('mins')),

                        \Filament\Forms\Components\TagsInput::make('tags')
                            ->label(__('Tags'))
                            ->placeholder('news, update, announcement'),

                        TextInput::make('year')
                            ->label(__('Year'))
                            ->numeric()
                            ->default(date('Y')),
                    ]),

                Section::make(__('Author & Settings'))
                    ->icon('heroicon-o-user')
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

                        Toggle::make('isFeatured')
                            ->label(__('Is Featured'))
                            ->inline(false),

                        Toggle::make('isTrending')
                            ->label(__('Is Trending'))
                            ->inline(false),

                        Toggle::make('isActive')
                            ->label(__('Is Active'))
                            ->default(true)
                            ->inline(false),
                    ]),

                Section::make(__('SEO Enhancement'))
                    ->icon('heroicon-o-magnifying-glass')
                    ->description(__('Search engine optimization settings'))
                    ->collapsed()
                    ->components([
                        TextInput::make('metaTitle')
                            ->label(__('Meta Title'))
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('metaTitle')),
                        Textarea::make('metaDescription')
                            ->label(__('Meta Description'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('metaDescription')),
                    ]),
            ]);
    }
}
