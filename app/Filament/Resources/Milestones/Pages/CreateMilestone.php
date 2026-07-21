<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Resources\Milestones\MilestoneResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateMilestone extends CreateRecord
{
    use Translatable;

    protected static string $resource = MilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
