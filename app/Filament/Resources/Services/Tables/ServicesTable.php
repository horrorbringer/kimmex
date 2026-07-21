<?php

namespace App\Filament\Resources\Services\Tables;

use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('title->en', $direction)),
                ToggleColumn::make('isActive')
                    ->label(__('Is Active'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),
                TextColumn::make('orderIndex')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('orderIndex', 'asc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
