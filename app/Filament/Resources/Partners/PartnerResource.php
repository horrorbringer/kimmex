<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Partners\Schemas\PartnerForm;
use App\Filament\Resources\Partners\Tables\PartnersTable;
use App\Models\Partner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = Partner::class;

    public static function getNavigationLabel(): string
    {
        return __('Partners');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Communication');
    }

    public static function getLabel(): ?string
    {
        return __('Partner');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Partners');
    }

    protected static ?int $navigationSort = 7;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';


    public static function form(Schema $schema): Schema
    {
        return PartnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnersTable::configure($table);
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
            'index' => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }
}
