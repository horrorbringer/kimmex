<?php

namespace App\Filament\Resources\MethodologySteps;

use App\Filament\Resources\MethodologySteps\Pages\CreateMethodologyStep;
use App\Filament\Resources\MethodologySteps\Pages\EditMethodologyStep;
use App\Filament\Resources\MethodologySteps\Pages\ListMethodologySteps;
use App\Filament\Resources\MethodologySteps\Schemas\MethodologyStepForm;
use App\Filament\Resources\MethodologySteps\Tables\MethodologyStepsTable;
use App\Models\MethodologyStep;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class MethodologyStepResource extends Resource
{
    use Translatable;

    protected static ?string $model = MethodologyStep::class;

    public static function getNavigationLabel(): string
    {
        return __('Methodology');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Portfolio');
    }

    public static function getLabel(): ?string
    {
        return __('Methodology Step');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Methodology Steps');
    }

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $recordTitleAttribute = 'title';

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return MethodologyStepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MethodologyStepsTable::configure($table);
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
            'index' => ListMethodologySteps::route('/'),
            'create' => CreateMethodologyStep::route('/create'),
            'edit' => EditMethodologyStep::route('/{record}/edit'),
        ];
    }
}
