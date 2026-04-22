<?php

namespace App\Filament\Resources\NewsArticles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Support\TranslationHelper;
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
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->helperText(__('Auto-generated from title. Used for the article URL.'))
                    ->unique(ignoreRecord: true)
                    ->required(),

                Textarea::make('excerpt')
                    ->label(__('Excerpt'))
                    ->hintAction(TranslationHelper::getAutoTranslateAction('excerpt'))
                    ->rows(3),

                FileUpload::make('coverImage')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('news/covers')
                    ->label(__('Cover Image')),

                RichEditor::make('content')
                    ->label(__('Content'))
                    ->required()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsDirectory('news/content'),

                FileUpload::make('gallery')
                    ->label(__('Gallery'))
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('news/gallery')
                    ->panelLayout('grid')
                    ->helperText(__('Upload multiple images for the article gallery')),

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
                            ->required(),

                        TextInput::make('readTime')
                            ->label(__('Read Time'))
                            ->suffix(__('mins')),

                        TextInput::make('tags')
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
                            ->default(auth()->user()?->employee?->id),

                        TextInput::make('authorName')
                            ->label(__('Author Name'))
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('authorName'))
                            ->dehydrated(),

                        Toggle::make('isFeatured')
                            ->label(__('Is Featured'))
                            ->inline(false),

                        Toggle::make('isTrending')
                            ->label(__('Is Trending'))
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
