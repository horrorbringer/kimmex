<?php

namespace App\Filament\Resources\NewsArticles\Tables;

use App\Filament\Support\FlatRecordDetails;
use App\Models\NewsArticle;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class NewsArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('newsCategory'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('title->en', $direction))
                    ->description(fn ($record) => $record->slug)
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
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make(),
                    Action::make('viewOnWebsite')
                        ->label(__('View on Website'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn (NewsArticle $record): string => route('news.show', ['slug' => $record->slug]))
                        ->openUrlInNewTab(),
                    Action::make('copy_link')
                        ->label(__('Copy Link'))
                        ->icon('heroicon-o-link')
                        ->color('gray')
                        ->extraAttributes(fn ($record) => [
                            'x-on:click' => "window.navigator.clipboard.writeText('".route('news.show', ['slug' => $record->slug])."')",
                        ])
                        ->action(fn () => Notification::make()->title(__('Link Copied'))->success()->send()),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->iconButton()
                    ->tooltip(__('Actions')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
