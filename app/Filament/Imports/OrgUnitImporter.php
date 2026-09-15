<?php

namespace App\Filament\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\OrgUnit;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class OrgUnitImporter extends Importer
{
    protected static ?string $model = OrgUnit::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->label('Position title (English)')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Finance Director')
                ->fillRecordUsing(function (OrgUnit $record, string $state): void {
                    $translations = is_array($record->title) ? $record->title : [];
                    $translations['en'] = trim($state);
                    if (empty($translations['km'])) {
                        $translations['km'] = trim($state);
                    }
                    $record->title = $translations;
                }),

            ImportColumn::make('title_km')
                ->label('Position title (Khmer)')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('នាយកហិរញ្ញវត្ថុ')
                ->ignoreBlankState()
                ->fillRecordUsing(function (OrgUnit $record, string $state): void {
                    $translations = is_array($record->title) ? $record->title : [];
                    $translations['km'] = trim($state);
                    $record->title = $translations;
                }),

            ImportColumn::make('type')
                ->label('Position type (EXECUTIVE, MANAGEMENT, DIRECTOR, MANAGER, STAFF, DEPARTMENT, OFFICE)')
                ->rules(['nullable', 'string', 'in:EXECUTIVE,MANAGEMENT,DIRECTOR,MANAGER,STAFF,DEPARTMENT,OFFICE'])
                ->example('DIRECTOR')
                ->ignoreBlankState()
                ->castStateUsing(fn (mixed $state): string => Str::upper(trim((string) $state)))
                ->fillRecordUsing(function (OrgUnit $record, string $state): void {
                    $record->type = $state;
                }),

            ImportColumn::make('parent_title')
                ->label('Reports to (Parent position title or supervisor name)')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Deputy General Manager (DGM)')
                ->ignoreBlankState()
                ->fillRecordUsing(function (OrgUnit $record, string $state): void {
                    $parentSearch = trim($state);
                    $parent = OrgUnit::where('title->en', $parentSearch)
                        ->orWhere('title->km', $parentSearch)
                        ->orWhereHas('employee', fn ($q) => $q->where('name', $parentSearch))
                        ->first();

                    if ($parent && $parent->id !== $record->id) {
                        $record->parentId = $parent->id;
                    }
                }),

            ImportColumn::make('employee_name')
                ->label('Assigned employee full name')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('TOUCH KIM')
                ->ignoreBlankState()
                ->fillRecordUsing(function (OrgUnit $record, string $state): void {
                    $empName = trim($state);
                    $employee = Employee::firstOrCreate(
                        ['name' => $empName],
                        [
                            'role' => is_array($record->title) ? ($record->title['en'] ?? '') : (string) $record->title,
                            'isActive' => true,
                        ]
                    );
                    $record->employeeId = $employee->id;
                }),

            ImportColumn::make('department_name')
                ->label('Related department name or slug')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Finance & Accounting')
                ->ignoreBlankState()
                ->fillRecordUsing(function (OrgUnit $record, string $state): void {
                    $deptName = trim($state);
                    $dept = Department::where('name->en', $deptName)
                        ->orWhere('name->km', $deptName)
                        ->orWhere('slug', Str::slug($deptName))
                        ->first();

                    if ($dept) {
                        $record->departmentId = $dept->id;
                    }
                }),

            ImportColumn::make('order_index')
                ->label('Sort priority order')
                ->numeric()
                ->rules(['nullable', 'integer'])
                ->example('1')
                ->ignoreBlankState()
                ->fillRecordUsing(function (OrgUnit $record, mixed $state): void {
                    $record->orderIndex = (int) $state;
                }),

            ImportColumn::make('is_active')
                ->label('Visible on public website org chart (1 or 0)')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->example('1')
                ->ignoreBlankState()
                ->fillRecordUsing(function (OrgUnit $record, bool $state): void {
                    $record->isActive = $state;
                }),
        ];
    }

    public function resolveRecord(): OrgUnit
    {
        $title = trim((string) ($this->data['title'] ?? ''));

        if (filled($title)) {
            $existing = OrgUnit::where('title->en', $title)
                ->orWhere('title->km', $title)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return new OrgUnit([
            'type' => 'STAFF',
            'orderIndex' => 0,
            'isActive' => true,
        ]);
    }

    protected function beforeSave(): void
    {
        if (! $this->getImport()->user()->where('role', 'ADMIN')->where('is_active', true)->exists()) {
            throw new RowImportFailedException('Only active administrators can import organization positions.');
        }
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Organization positions import complete';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = Number::format($import->successful_rows).' '.str('position')->plural($import->successful_rows).' created or updated.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
