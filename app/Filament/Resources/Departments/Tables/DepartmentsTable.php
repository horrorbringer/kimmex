<?php

namespace App\Filament\Resources\Departments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use App\Models\Department;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Department Name'))
                    ->description(fn(Department $record) => \Illuminate\Support\Str::limit(strip_tags($record->description['en'] ?? ''), 50))
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label(__('Public URL'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('orgUnits_count')
                    ->label(__('Active Units'))
                    ->counts('orgUnits')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('jobPostings_count')
                    ->label(__('Job Openings'))
                    ->counts('jobPostings')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('Last Edit'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
