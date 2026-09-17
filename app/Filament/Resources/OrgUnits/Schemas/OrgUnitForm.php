<?php

namespace App\Filament\Resources\OrgUnits\Schemas;

use App\Filament\Support\TranslationHelper;
use App\Models\Employee;
use App\Models\OrgUnit;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
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

    public static function getSchema(?string $context = null): array
    {
        return [
            Grid::make(['default' => 1, 'sm' => 2])->components([
                Select::make('employeeId')
                    ->label(__('Assigned Employee'))
                    ->placeholder(__('Choose employee (optional)...'))
                    ->relationship('employee', 'name')
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
                    ->placeholder(__('e.g. Chief Executive Officer, Project Director'))
                    ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                    ->required(),

                Select::make('type')
                    ->label(__('Unit Type'))
                    ->options([
                        'EXECUTIVE' => __('Executive / C-Suite'),
                        'MANAGEMENT' => __('Senior Management'),
                        'DIRECTOR' => __('Director'),
                        'MANAGER' => __('Manager / Lead'),
                        'STAFF' => __('Staff / Officer'),
                        'DEPARTMENT' => __('Department / Division'),
                        'OFFICE' => __('Office / Branch'),
                    ])
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->default('STAFF')
                    ->required(),

                Select::make('parentId')
                    ->label(__('Reports To (Parent Position)'))
                    ->relationship('parent', 'title', fn ($query, ?Model $record) => $query->orderBy('title->en')->when($record, fn ($q) => $q->where('id', '!=', $record->id)))
                    ->searchable()
                    ->preload()
                    ->placeholder(__('None (Top Root Position)'))
                    ->disabled(fn () => $context === 'child')
                    ->dehydrated()
                    ->visible(fn () => $context !== 'root'),

                Select::make('departmentId')
                    ->label(__('Related Department'))
                    ->relationship('department', 'name', fn ($query) => $query->orderBy('name->en'))
                    ->searchable()
                    ->preload()
                    ->placeholder(__('Optional department link...')),

                Select::make('chart_group')
                    ->label(__('Chart Group'))
                    ->options(fn () => OrgUnit::getChartGroupOptions())
                    ->allowHtml(false)
                    ->native(false)
                    ->default('main')
                    ->required()
                    ->searchable(),

                TextInput::make('orderIndex')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('isActive')
                    ->label(__('Visible on Public Website'))
                    ->default(true)
                    ->columnSpanFull(),
            ]),
        ];
    }
}
