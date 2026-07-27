<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditProject extends EditRecord
{
    use Translatable;

    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
