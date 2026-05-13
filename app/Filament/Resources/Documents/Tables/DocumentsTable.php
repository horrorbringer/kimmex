<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnailUrl')
                    ->label(__('Thumbnail'))
                    ->disk('public')
                    ->circular(),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn($query, $direction) => $query->orderBy('title->en', $direction))
                    ->description(fn($record) => $record->slug),

                TextColumn::make('documentCategory.name')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->searchable(),
                ToggleColumn::make('isPublic')
                    ->label(__('Public')),
                ToggleColumn::make('is_featured')
                    ->label(__('Featured')),
                ToggleColumn::make('isActive')
                    ->label(__('Active')),
                TextColumn::make('downloadCount')
                    ->label(__('Downloads'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                \Filament\Actions\Action::make('view_on_website')
                    ->label(__('View on Website'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn($record) => url("/documents/{$record->slug}"))
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('copy_link')
                    ->label(__('Copy Link'))
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->extraAttributes(fn($record) => [
                        'x-on:click' => "window.navigator.clipboard.writeText('" . url("/documents/{$record->slug}") . "')",
                    ])
                    ->action(fn() => \Filament\Notifications\Notification::make()->title(__('Link Copied'))->success()->send()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
