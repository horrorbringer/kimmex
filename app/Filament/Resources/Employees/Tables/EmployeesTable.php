<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['orgUnit.parent.parent.parent']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->role),

                TextColumn::make('orgUnit.title')
                    ->label(__('Org Position'))
                    ->description(fn ($record) => $record->orgUnit?->getPath() ?? __('Not Assigned'))
                    ->placeholder('-')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('specialization')
                    ->label(__('Specialization'))
                    ->badge()
                    ->searchable(),
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
