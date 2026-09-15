<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Full name'),
            ExportColumn::make('role')
                ->label('Job title'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('phone')
                ->label('Phone'),
            ExportColumn::make('location')
                ->label('Location'),
            ExportColumn::make('specialization')
                ->label('Specialization'),
            ExportColumn::make('experience')
                ->label('Experience'),
            ExportColumn::make('image')
                ->label('Profile photo URL'),
            ExportColumn::make('isActive')
                ->label('Show on organization chart'),
            ExportColumn::make('orgUnit.title')
                ->label('Organization position'),
        ];
    }

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Employee export complete';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
