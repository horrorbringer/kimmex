<?php

namespace App\Filament\Resources\OrgUnits\Pages;

use App\Filament\Pages\ManageOrgChart;
use App\Filament\Resources\OrgUnits\OrgUnitResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListOrgUnits extends ListRecords
{
    use Translatable;

    protected static string $resource = OrgUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Action::make('visualOrgChart')
                ->label(__('Visual Org Chart'))
                ->icon('heroicon-o-presentation-chart-line')
                ->color('primary')
                ->url(ManageOrgChart::getUrl()),
            CreateAction::make(),
        ];
    }
}
