<?php

namespace App\Filament\Resources\Departments\RelationManagers;

use App\Filament\Resources\OrgUnits\Schemas\OrgUnitForm;
use App\Filament\Resources\OrgUnits\Tables\OrgUnitsTable;
use App\Filament\Support\FlatRecordDetails;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrgUnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'orgUnits';

    public function form(Schema $schema): Schema
    {
        return OrgUnitForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return OrgUnitsTable::configure($table)
            ->recordTitleAttribute('title')
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
