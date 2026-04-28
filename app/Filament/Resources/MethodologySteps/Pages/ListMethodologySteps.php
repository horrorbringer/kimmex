<?php

namespace App\Filament\Resources\MethodologySteps\Pages;

use App\Filament\Resources\MethodologySteps\MethodologyStepResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMethodologySteps extends ListRecords
{
    protected static string $resource = MethodologyStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
