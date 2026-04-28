<?php

namespace App\Filament\Resources\MethodologySteps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class MethodologyStepsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orderIndex')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(query: fn($query, $direction) => $query->orderBy('title->en', $direction)),
                TextColumn::make('icon')
                    ->fontFamily(\Filament\Support\Enums\FontFamily::Mono),
                ToggleColumn::make('isActive'),
            ])
            ->reorderable('orderIndex')
            ->defaultSort('orderIndex')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
