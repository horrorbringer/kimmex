<?php

namespace App\Filament\Resources\MethodologySteps\Pages;

use App\Filament\Resources\MethodologySteps\MethodologyStepResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMethodologyStep extends EditRecord
{
    protected static string $resource = MethodologyStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
