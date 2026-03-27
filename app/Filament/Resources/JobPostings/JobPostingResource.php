<?php

namespace App\Filament\Resources\JobPostings;

use App\Filament\Resources\JobPostings\Pages\CreateJobPosting;
use App\Filament\Resources\JobPostings\Pages\EditJobPosting;
use App\Filament\Resources\JobPostings\Pages\ListJobPostings;
use App\Filament\Resources\JobPostings\Schemas\JobPostingForm;
use App\Filament\Resources\JobPostings\Tables\JobPostingsTable;
use App\Models\JobPosting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JobPostingResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = JobPosting::class;

    public static function getNavigationLabel(): string
    {
        return __('Job Postings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Governance');
    }

    public static function getLabel(): ?string
    {
        return __('Job Posting');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Job Postings');
    }

    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Schema $schema): Schema
    {
        return JobPostingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobPostingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobPostings::route('/'),
            'create' => CreateJobPosting::route('/create'),
            'edit' => EditJobPosting::route('/{record}/edit'),
        ];
    }
}
