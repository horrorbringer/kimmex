<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Resources\Milestones\MilestoneResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMilestone extends EditRecord
{
    use \LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

    protected static string $resource = MilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
