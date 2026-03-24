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
                Section::make(__('Organization Unit'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required(),
                            Select::make('type')
                                ->label(__('Type'))
                                ->options([
                                    'STAFF' => __('Staff / Individual'),
                                    'DEPARTMENT' => __('Department / Group'),
                                    'OFFICE' => __('Office / Branch'),
                                ])
                                ->required()
                                ->default('STAFF'),
                        ]),
                    ]),

                Section::make(__('Structure & Linking'))
                    ->components([
                        Grid::make(3)->components([
                            Select::make('parentId')
                                ->label(__('Parent Unit'))
                                ->relationship('parent', 'title')
                                ->searchable()
                                ->preload(),
                            Select::make('employeeId')
                                ->label(__('Linked Employee'))
                                ->relationship('employee', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('departmentId')
                                ->label(__('Linked Department'))
                                ->relationship('department', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                        TextInput::make('orderIndex')
                            ->label(__('Order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
