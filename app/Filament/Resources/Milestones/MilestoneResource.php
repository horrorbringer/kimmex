<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\Resources\Milestones\Pages\CreateMilestone;
use App\Filament\Resources\Milestones\Pages\EditMilestone;
use App\Filament\Resources\Milestones\Pages\ListMilestones;
use App\Filament\Resources\Milestones\Schemas\MilestoneForm;
use App\Filament\Resources\Milestones\Tables\MilestonesTable;
use App\Models\Milestone;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MilestoneResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = Milestone::class;

    public static function getNavigationLabel(): string
    {
        return __('Milestones');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Portfolio');
    }

    public static function getLabel(): ?string
    {
        return __('Milestone');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Milestones');
    }

    protected static ?int $navigationSort = 5;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-flag';

    public static function form(Schema $schema): Schema
    {
        return MilestoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MilestonesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMilestones::route('/'),
            'create' => CreateMilestone::route('/create'),
            'edit' => EditMilestone::route('/{record}/edit'),
        ];
    }
}
