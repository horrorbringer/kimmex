<?php

namespace App\Filament\Resources\JobApplications;

use App\Filament\Resources\JobApplications\Pages\CreateJobApplication;
use App\Filament\Resources\JobApplications\Pages\EditJobApplication;
use App\Filament\Resources\JobApplications\Pages\ListJobApplications;
use App\Filament\Resources\JobApplications\Schemas\JobApplicationForm;
use App\Filament\Resources\JobApplications\Tables\JobApplicationsTable;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    public static function getNavigationLabel(): string
    {
        return __('Job Applications');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Governance');
    }

    public static function getLabel(): ?string
    {
        return __('Job Application');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Job Applications');
    }

    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    public static function form(Schema $schema): Schema
    {
        return JobApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplications::route('/'),
            'edit' => EditJobApplication::route('/{record}/edit'),
        ];
    }
}
