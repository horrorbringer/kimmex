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
            ->components([
                Section::make(__('Article Content'))
                    ->description(__('Main content and identity of the article'))
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
                        Textarea::make('excerpt')
                            ->label(__('Excerpt'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('excerpt'))
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label(__('Content'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Media & Gallery'))
                    ->description(__('Visual assets for the article'))
                    ->components([
                        FileUpload::make('coverImage')
                            ->image()
                            ->disk('public')
                            ->directory('news/covers')
                            ->label(__('Cover Image')),
                        FileUpload::make('gallery')
                            ->label(__('Gallery'))
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('news/gallery')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Publishing & Authorship'))
                    ->components([
                        Grid::make(3)->components([
                            DateTimePicker::make('publishedAt')
                                ->label(__('Published At'))
                                ->required()
                                ->default(now()),
                            TextInput::make('category')
                                ->label(__('Category'))
                                ->required(),
                            TextInput::make('readTime')
                                ->label(__('Read Time'))
                                ->suffix(__('mins')),
                        ]),
                        Grid::make(2)->components([
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
                                ->disabled()
                                ->dehydrated(),
                        ]),
                        TextInput::make('tags')
                            ->label(__('Tags'))
                            ->placeholder('news, update, announcement'),
                    ]),

                Section::make(__('Visibility & Settings'))
                    ->components([
                        Grid::make(3)->components([
                            Toggle::make('isFeatured')
                                ->label(__('Is Featured')),
                            Toggle::make('isTrending')
                                ->label(__('Is Trending')),
                            TextInput::make('year')
                                ->label(__('Year'))
                                ->numeric()
                                ->default(date('Y')),
                        ]),
                    ]),

                Section::make(__('SEO Enhancement'))
                    ->collapsed()
                    ->components([
                        TextInput::make('metaTitle')
                            ->label(__('Meta Title'))
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('metaTitle')),
                        Textarea::make('metaDescription')
                            ->label(__('Meta Description'))
                            ->hintAction(TranslationHelper::getAutoTranslateAction('metaDescription'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
