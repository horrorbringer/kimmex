<?php

namespace App\Filament\Resources\OrgUnits\Tables;

use App\Models\OrgUnit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrgUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->description(fn(OrgUnit $record) => $record->parent?->title ?? __('Root Unit'))
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('Unit Type'))
                    ->badge()
                    ->colors([
                        'primary' => 'STAFF',
                        'success' => 'DEPARTMENT',
                        'warning' => 'OFFICE',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'STAFF' => __('Individual'),
                        'DEPARTMENT' => __('Department'),
                        'OFFICE' => __('Facility'),
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'STAFF' => 'heroicon-o-user',
                        'DEPARTMENT' => 'heroicon-o-building-office-2',
                        'OFFICE' => 'heroicon-o-map-pin',
                    }),

                TextColumn::make('employee.name')
                    ->label(__('Assigned Employee'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label(__('Related Dept'))
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('orderIndex')
                    ->label(__('Sort'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Last Update'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label(__('Filter by Type'))
                    ->options([
                        'STAFF' => __('Individual (Staff)'),
                        'DEPARTMENT' => __('Departmental Group'),
                        'OFFICE' => __('Facility / Office'),
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('departmentId')
                    ->label(__('Department'))
                    ->relationship('department', 'name', fn($query) => $query->orderBy('name->en')),
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
