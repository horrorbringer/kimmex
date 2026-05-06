<?php

namespace App\Filament\Resources\MethodologySteps\Pages;

use App\Filament\Resources\MethodologySteps\MethodologyStepResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditMethodologyStep extends EditRecord
{
    use Translatable;

    protected static string $resource = MethodologyStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }
}
