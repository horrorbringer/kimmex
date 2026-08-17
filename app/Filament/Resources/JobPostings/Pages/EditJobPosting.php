<?php

namespace App\Filament\Resources\JobPostings\Pages;

use App\Filament\Resources\JobPostings\JobPostingResource;
use App\Filament\Support\AIHelper;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditJobPosting extends EditRecord
{
    use Translatable;

    protected static string $resource = JobPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            AIHelper::getAutoFillAction('job'),
            DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
