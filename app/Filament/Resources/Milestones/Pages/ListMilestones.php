<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Resources\Milestones\MilestoneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListMilestones extends ListRecords
{
    use Translatable;

    protected static string $resource = MilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
