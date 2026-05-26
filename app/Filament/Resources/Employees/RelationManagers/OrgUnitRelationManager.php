<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrgUnitRelationManager extends RelationManager
{
    protected static string $relationship = 'orgUnit';
    protected static ?string $title = 'Organization Position';

    public function form(Schema $schema): Schema
    {
        return \App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\OrgUnits\Tables\OrgUnitsTable::configure($table)
            ->recordTitleAttribute('title')
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make(),
                \Filament\Tables\Actions\AssociateAction::make(),
            ])
            ->recordActions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DissociateAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DissociateBulkAction::make(),
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
