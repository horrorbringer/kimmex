<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('heroImage')
                    ->label(__('Photo'))
                    ->getStateUsing(fn ($record) => \App\Support\PublicStorage::url($record->heroImage))
                    ->disk(config('filesystems.public_uploads_disk'))
                    ->circular(),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn($query, $direction) => $query->orderBy('title->en', $direction))
                    ->description(fn($record) => $record->slug),
                TextColumn::make('projectCategory.name')
                    ->label(__('Category'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('location')
                    ->label(__('Location'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('client')
                    ->label(__('Client'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('completionDate')
                    ->label(__('Completion Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
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
                    ->url(fn($record) => url("/projects/{$record->slug}"))
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('copy_link')
                    ->label(__('Copy Link'))
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->extraAttributes(fn($record) => [
                        'x-on:click' => "window.navigator.clipboard.writeText('" . url("/projects/{$record->slug}") . "')",
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
