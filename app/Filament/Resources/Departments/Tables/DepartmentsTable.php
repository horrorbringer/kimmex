<?php

namespace App\Filament\Resources\Departments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use App\Models\Department;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->with(['headUnit.employee'])
                ->withCount(['orgUnits', 'jobPostings']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Department Name'))
                    ->description(fn(Department $record) => \Illuminate\Support\Str::limit(strip_tags($record->description['en'] ?? ''), 50))
                    ->searchable()
                    ->weight('bold')
                    ->toggleable(),

                TextColumn::make('headUnit.employee.name')
                    ->label(__('Department Head'))
                    ->description(fn(Department $record) => $record->headUnit?->employee?->role ?? '-')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('orgUnits_count')
                    ->label(__('Active Units'))
                    ->counts('orgUnits')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

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
                EditAction::make()->visible(fn() => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn() => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
