<?php

namespace App\Filament\Resources\ProjectImages\Pages;

use App\Filament\Resources\ProjectImages\ProjectImageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectImage extends CreateRecord
{
    protected static string $resource = ProjectImageResource::class;
}
