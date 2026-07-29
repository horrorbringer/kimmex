<?php

namespace App\Filament\Resources\Departments\Tables;

use App\Filament\Support\FlatRecordDetails;
use App\Models\Department;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                    ->description(fn (Department $record) => Str::limit(strip_tags($record->description['en'] ?? ''), 50))
                    ->searchable()
                    ->weight('bold')
                    ->toggleable(),

                TextColumn::make('headUnit.employee.name')
                    ->label(__('Department Head'))
                    ->description(fn (Department $record) => $record->headUnit?->employee?->role ?? '-')
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
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->iconButton()
                    ->tooltip(__('Actions')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
