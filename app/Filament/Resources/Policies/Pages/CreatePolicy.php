<?php

namespace App\Filament\Resources\Policies\Pages;

use App\Filament\Resources\Policies\PolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicy extends CreateRecord
{
    use \LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

    protected static string $resource = PolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
