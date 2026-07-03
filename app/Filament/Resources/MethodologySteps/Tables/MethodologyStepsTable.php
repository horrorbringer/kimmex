<?php

namespace App\Filament\Resources\MethodologySteps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(query: fn($query, $direction) => $query->orderBy('title->en', $direction)),
                TextColumn::make('icon')
                    ->label(__('Icon'))
                    ->fontFamily(\Filament\Support\Enums\FontFamily::Mono),
                ToggleColumn::make('isActive')
                    ->label(__('Is Active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->reorderable('orderIndex')
            ->defaultSort('orderIndex')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()->schema(fn ($record): array => \App\Filament\Support\FlatRecordDetails::schema($record)),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
