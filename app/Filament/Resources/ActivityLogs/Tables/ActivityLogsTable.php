<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label(__('Log Name'))
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label(__('Subject Type'))
                    ->searchable(),
                TextColumn::make('subject_id')
                    ->label(__('Subject ID'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('causer_type')
                    ->label(__('Causer Type'))
                    ->searchable(),
                TextColumn::make('causer_id')
                    ->label(__('Causer ID'))
                    ->numeric()
                    ->sortable(),
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
                TextColumn::make('event')
                    ->label(__('Event'))
                    ->searchable(),
                TextColumn::make('batch_uuid')
                    ->label(__('Batch UUID'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
