<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Imports\EmployeeImporter;
use App\Filament\Support\FlatRecordDetails;
use App\Models\Employee;
use App\Support\PublicStorage;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['orgUnit.parent.parent.parent']))
            ->defaultSort('name')
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Photo'))
                    ->getStateUsing(fn (Employee $record): ?string => ($imageUrl = PublicStorage::urlIfExists($record->image)) ? url($imageUrl) : null)
                    ->defaultImageUrl(asset('images/employee-placeholder.svg'))
                    ->circular()
                    ->imageSize(42),
                TextColumn::make('name')
                    ->label(__('Employee'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->role),

                TextColumn::make('orgUnit.title')
                    ->label(__('Organization Position'))
                    ->description(fn ($record) => $record->orgUnit?->getPath() ?? __('Not Assigned'))
                    ->placeholder(__('Not assigned yet'))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Contact'))
                    ->searchable()
                    ->copyable()
                    ->description(fn (Employee $record): ?string => $record->phone)
                    ->toggleable(),
                TextColumn::make('specialization')
                    ->label(__('Expertise'))
                    ->badge()
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                ToggleColumn::make('isActive')
                    ->label(__('Visible'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                TernaryFilter::make('isActive')
                    ->label(__('Visibility'))
                    ->placeholder(__('All employees'))
                    ->trueLabel(__('Visible on organization chart'))
                    ->falseLabel(__('Hidden from organization chart')),
            ])
            ->headerActions([
                ImportAction::make('importEmployees')
                    ->label(__('Import Employees'))
                    ->importer(EmployeeImporter::class)
                    ->fileRules(['max:5120'])
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ])
            ->emptyStateHeading(__('No employees yet'))
            ->emptyStateDescription(__('Create an employee profile first. You can assign their organization position after saving.'))
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                    EditAction::make(),
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
