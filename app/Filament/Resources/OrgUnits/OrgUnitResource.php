<?php

namespace App\Filament\Resources\OrgUnits;

use App\Filament\Resources\OrgUnits\Pages\CreateOrgUnit;
use App\Filament\Resources\OrgUnits\Pages\EditOrgUnit;
use App\Filament\Resources\OrgUnits\Pages\ListOrgUnits;
use App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm;
use App\Filament\Resources\OrgUnits\Tables\OrgUnitsTable;
use App\Models\OrgUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrgUnitResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = OrgUnit::class;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return __('Org Units');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('HR Management');
    }

    public static function getLabel(): ?string
    {
        return __('Org Unit');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Org Units');
    }

    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin();
    }
    public static function form(Schema $schema): Schema
    {
        return OrgUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrgUnitsTable::configure($table);
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
            'index' => ListOrgUnits::route('/'),
            'create' => CreateOrgUnit::route('/create'),
            'edit' => EditOrgUnit::route('/{record}/edit'),
        ];
    }
}
