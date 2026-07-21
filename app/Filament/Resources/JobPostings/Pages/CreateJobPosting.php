<?php

namespace App\Filament\Resources\JobPostings\Pages;

use App\Filament\Resources\JobPostings\JobPostingResource;
use App\Filament\Support\AIHelper;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateJobPosting extends CreateRecord
{
    use Translatable;

    protected static string $resource = JobPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AIHelper::getAutoFillAction('job'),
        ];
    }
}
