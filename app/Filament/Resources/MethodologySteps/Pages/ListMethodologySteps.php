<?php

namespace App\Filament\Resources\MethodologySteps\Pages;

use App\Filament\Resources\MethodologySteps\MethodologyStepResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListMethodologySteps extends ListRecords
{
    use Translatable;

    protected static string $resource = MethodologyStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
