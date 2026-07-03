<?php

namespace App\Filament\Resources\Milestones\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class MilestonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')
                    ->label(__('Year'))
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sortOrder')
                    ->label(__('Order'))
                    ->sortable(),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()->schema(fn ($record): array => \App\Filament\Support\FlatRecordDetails::schema($record)),
                EditAction::make(),
                DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
