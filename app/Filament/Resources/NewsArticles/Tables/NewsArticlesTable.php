<?php

namespace App\Filament\Resources\NewsArticles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('coverImage')
                    ->label(__('Cover Image'))
                    ->circular(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn($query, $direction) => $query->orderBy('title->en', $direction))
                    ->description(fn($record) => $record->slug)
                    ->wrap(),
                TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('authorName')
                    ->label(__('Author'))
                    ->searchable(),
                TextColumn::make('publishedAt')
                    ->label(__('Published At'))
                    ->dateTime('M d, Y')
                    ->sortable(),
                IconColumn::make('isFeatured')
                    ->boolean()
                    ->label(__('Is Featured')),
                IconColumn::make('isTrending')
                    ->boolean()
                    ->label(__('Is Trending')),
                TextColumn::make('readTime')
                    ->label(__('Read Time'))
                    ->suffix(__(' mins'))
                    ->toggleable(),
                TextColumn::make('year')
                    ->label(__('Year'))
                    ->toggleable(),
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\Action::make('viewOnWebsite')
                    ->label(__('View on Website'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn(\App\Models\NewsArticle $record): string => route('news.show', ['slug' => $record->slug]))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
