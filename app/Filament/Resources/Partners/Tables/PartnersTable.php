<?php

namespace App\Filament\Resources\Partners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->searchable(),
                TextColumn::make('orderIndex')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()->schema(fn ($record): array => \App\Filament\Support\FlatRecordDetails::schema($record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
