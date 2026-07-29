<?php

namespace App\Filament\Resources\OrgUnits\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class OrgUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(static::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('Basic Information'))
                ->icon('heroicon-o-identification')
                ->description(__('Choose the position type and employee. The employee job title fills automatically.'))
                ->components([
                    Grid::make(3)->components([
                        Select::make('type')
                            ->label(__('Unit Type'))
                            ->options([
                                'EXECUTIVE' => __('C-Suite / Executive Board'),
                                'MANAGEMENT' => __('Senior Management'),
                                'DIRECTOR' => __('Department Director'),
                                'MANAGER' => __('Manager / Lead'),
                                'STAFF' => __('Individual (Staff)'),
                                'DEPARTMENT' => __('Departmental Group'),
                                'OFFICE' => __('Facility / Office'),
                            ])
                            ->native(false)
                            ->selectablePlaceholder(false)
                            ->live()
                            ->required()
                            ->default('STAFF'),
                        Select::make('employeeId')
                            ->label(__('Assigned Employee'))
                            ->helperText(__('Select an employee to fill the position title automatically.'))
                            ->relationship('employee', 'name')
                            ->visible(fn ($get) => in_array($get('type'), ['EXECUTIVE', 'MANAGEMENT', 'DIRECTOR', 'MANAGER', 'STAFF']))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $employee = Employee::find($state);

                                if ($employee) {
                                    $set('title', filled($employee->role) ? $employee->role : $employee->name);
                                }
                            }),
                        TextInput::make('title')
                            ->label(__('Position Title'))
                            ->placeholder(__('E.g., Engineering Lead, HR Group'))
                            ->helperText(__('Filled from the employee job title. Change it only when this organization position needs a different name.'))
                            ->required(),
                    ]),
                ]),

            Section::make(__('Hierarchy & Connections'))
                ->icon('heroicon-o-swatch')
                ->description(__('Connect this unit to the larger organizational tree and link it to employees or departments.'))
                ->components([
                    Grid::make(2)->components([
                        Select::make('parentId')
                            ->label(__('Reports To (Parent Unit)'))
                            ->relationship('parent', 'title', fn ($query, ?Model $record) => $query->orderBy('title->en')->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder(__('Select parent node...'))
                            ->columnSpanFull(),

                        Select::make('departmentId')
                            ->label(__('Related Department'))
                            ->helperText(__('Link a formal department structure to this unit.'))
                            ->relationship('department', 'name', fn ($query) => $query->orderBy('name->en'))
                            ->visible(fn ($get) => in_array($get('type'), ['DEPARTMENT', 'DIRECTOR', 'MANAGER']))
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
                    Toggle::make('isActive')
                        ->label(__('Is Active'))
                        ->default(true)
                        ->required(),
                ]),
        ];
    }
}
