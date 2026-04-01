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
                \Filament\Tables\Columns\ImageColumn::make('employee.image')
                    ->label('')
                    ->circular()
                    ->placeholder('-'),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->description(fn(OrgUnit $record) => $record->getPath())
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('Tier / Type'))
                    ->badge()
                    ->colors([
                        'danger' => 'EXECUTIVE',
                        'warning' => 'MANAGEMENT',
                        'success' => 'DIRECTOR',
                        'info' => 'MANAGER',
                        'primary' => 'STAFF',
                        'secondary' => 'DEPARTMENT',
                        'gray' => 'OFFICE',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'EXECUTIVE' => __('Executive'),
                        'MANAGEMENT' => __('Management'),
                        'DIRECTOR' => __('Director'),
                        'MANAGER' => __('Manager'),
                        'STAFF' => __('Individual'),
                        'DEPARTMENT' => __('Department'),
                        'OFFICE' => __('Facility'),
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'EXECUTIVE' => 'heroicon-o-sparkles',
                        'MANAGEMENT' => 'heroicon-o-shield-check',
                        'DIRECTOR' => 'heroicon-o-academic-cap',
                        'MANAGER' => 'heroicon-o-identification',
                        'STAFF' => 'heroicon-o-user',
                        'DEPARTMENT' => 'heroicon-o-building-office-2',
                        'OFFICE' => 'heroicon-o-map-pin',
                    }),

                TextColumn::make('employee.name')
                    ->label(__('Assigned Employee'))
                    ->placeholder('-')
                    ->weight('semibold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label(__('Related Dept'))
                    ->placeholder('-')
                    ->color('gray')
                    ->searchable(),

                \Filament\Tables\Columns\TextInputColumn::make('orderIndex')
                    ->label(__('Sort'))
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('Last Update'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('orderIndex')
            ->groups([
                \Filament\Tables\Grouping\Group::make('type')
                    ->label(__('Organizational Tier'))
                    ->collapsible(),
                \Filament\Tables\Grouping\Group::make('department.name')
                    ->label(__('Department'))
                    ->collapsible(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label(__('Filter by Tier'))
                    ->options([
                        'EXECUTIVE' => __('C-Suite'),
                        'MANAGEMENT' => __('Senior Management'),
                        'DIRECTOR' => __('Directors'),
                        'MANAGER' => __('Managers'),
                        'STAFF' => __('Staff'),
                        'DEPARTMENT' => __('Departments'),
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
