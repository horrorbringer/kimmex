<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Full name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Sok Dara'),
            ImportColumn::make('role')
                ->label('Job title')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Project Manager'),
            ImportColumn::make('email')
                ->label('Email')
                ->rules(['nullable', 'email', 'max:255'])
                ->example('sok.dara@kimmex.com.kh')
                ->ignoreBlankState()
                ->castStateUsing(fn (mixed $state): string => Str::lower(trim((string) $state))),
            ImportColumn::make('phone')
                ->label('Phone')
                ->rules(['nullable', 'string', 'max:50'])
                ->example('+855 12 345 678')
                ->ignoreBlankState(),
            ImportColumn::make('location')
                ->label('Location')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Phnom Penh')
                ->ignoreBlankState(),
            ImportColumn::make('specialization')
                ->label('Specialization')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Construction Management')
                ->ignoreBlankState(),
            ImportColumn::make('experience')
                ->label('Experience')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('5 Years')
                ->ignoreBlankState(),
            ImportColumn::make('is_active')
                ->label('Show on organization chart')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->example('1')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Employee $record, bool $state): void {
                    $record->isActive = $state;
                }),
        ];
    }

    public function resolveRecord(): Employee
    {
        $email = $this->data['email'] ?? null;

        if (filled($email)) {
            return Employee::query()->firstOrNew([
                'email' => $email,
            ]);
        }

        return new Employee;
    }

    protected function beforeSave(): void
    {
        if (! $this->getImport()->user()->where('role', 'ADMIN')->where('is_active', true)->exists()) {
            throw new RowImportFailedException('Only active administrators can import employees.');
        }
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Employee import complete';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = Number::format($import->successful_rows).' '.str('employee')->plural($import->successful_rows).' created or updated.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
