<?php

namespace App\Filament\Resources\Policies;

use App\Filament\Resources\Policies\Pages\CreatePolicy;
use App\Filament\Resources\Policies\Pages\EditPolicy;
use App\Filament\Resources\Policies\Pages\ListPolicies;
use App\Filament\Resources\Policies\Schemas\PolicyForm;
use App\Filament\Resources\Policies\Tables\PoliciesTable;
use App\Models\Policy;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PolicyResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = Policy::class;

    public static function getNavigationLabel(): string
    {
        return __('Company Policies');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Governance');
    }

    public static function getLabel(): ?string
    {
        return __('Policy');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Policies');
    }

    protected static ?int $navigationSort = 10;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    public static function form(Schema $schema): Schema
    {
        return PolicyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PoliciesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPolicies::route('/'),
            'create' => CreatePolicy::route('/create'),
            'edit' => EditPolicy::route('/{record}/edit'),
        ];
    }
}
