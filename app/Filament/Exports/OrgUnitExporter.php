<?php

namespace App\Filament\Exports;

use App\Models\OrgUnit;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class OrgUnitExporter extends Exporter
{
    protected static ?string $model = OrgUnit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title')
                ->label('Position title')
                ->state(fn (OrgUnit $record): string => (string) ($record->getTranslation('title', 'en') ?: $record->title)),
            ExportColumn::make('title_km')
                ->label('Position title (Khmer)')
                ->state(fn (OrgUnit $record): string => (string) ($record->getTranslation('title', 'km') ?: '')),
            ExportColumn::make('type')
                ->label('Position type'),
            ExportColumn::make('parent.title')
                ->label('Reports to')
                ->state(fn (OrgUnit $record): string => $record->parent ? (string) ($record->parent->getTranslation('title', 'en') ?: $record->parent->title) : ''),
            ExportColumn::make('employee.name')
                ->label('Assigned employee'),
            ExportColumn::make('department.name')
                ->label('Department')
                ->state(fn (OrgUnit $record): string => $record->department ? (string) ($record->department->getTranslation('name', 'en') ?: $record->department->name) : ''),
            ExportColumn::make('orderIndex')
                ->label('Sort order'),
            ExportColumn::make('isActive')
                ->label('Active on website'),
        ];
    }

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Organization structure export complete';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your organization structure export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
