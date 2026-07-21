<?php

namespace App\Filament\Resources\OrgUnits\Pages;

use App\Filament\Resources\OrgUnits\OrgUnitResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateOrgUnit extends CreateRecord
{
    use Translatable;

    protected static string $resource = OrgUnitResource::class;
}
