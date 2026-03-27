<?php

namespace App\Filament\Resources\OrgUnits\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrgUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->icon('heroicon-o-identification')
                    ->description(__('Define the name and classification of this organization unit.'))
                    ->components([
                        Grid::make(3)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->placeholder(__('E.g., Engineering Lead, HR Group'))
                                ->columnSpan(2)
                                ->required(),
                            Select::make('type')
                                ->label(__('Unit Type'))
                                ->options([
                                    'STAFF' => __('Individual (Staff)'),
                                    'DEPARTMENT' => __('Departmental Group'),
                                    'OFFICE' => __('Facility / Office'),
                                ])
                                ->native(false)
                                ->selectablePlaceholder(false)
                                ->required()
                                ->default('STAFF'),
                        ]),
                    ]),

                Section::make(__('Hierarchy & Connections'))
                    ->icon('heroicon-o-swatch')
                    ->description(__('Connect this unit to the larger organizational tree and link it to employees or departments.'))
                    ->components([
                        Grid::make(2)->components([
                            Select::make('parentId')
                                ->label(__('Reports To (Parent Unit)'))
                                ->relationship('parent', 'title', fn($query) => $query->orderBy('title->en'))
                                ->searchable()
                                ->preload()
                                ->placeholder(__('Select parent node...'))
                                ->columnSpanFull(),

                            Select::make('employeeId')
                                ->label(__('Assigned Employee'))
                                ->helperText(__('Link an individual employee to this unit.'))
                                ->relationship('employee', 'name')
                                ->visible(fn($get) => $get('type') === 'STAFF')
                                ->searchable()
                                ->preload(),

                            Select::make('departmentId')
                                ->label(__('Related Department'))
                                ->helperText(__('Link a formal department structure to this unit.'))
                                ->relationship('department', 'name', fn($query) => $query->orderBy('name->en'))
                                ->visible(fn($get) => $get('type') !== 'STAFF')
                                ->searchable()
                                ->preload(),
                        ]),
                    ]),

                Section::make(__('Display Settings'))
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->collapsed()
                    ->components([
                        TextInput::make('orderIndex')
                            ->label(__('Sort Priority'))
                            ->helperText(__('Lower numbers appear first in lists.'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
