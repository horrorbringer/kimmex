<?php

namespace App\Filament\Resources\NewsArticles\Pages;

use App\Filament\Resources\NewsArticles\NewsArticleResource;
use App\Filament\Support\AIHelper;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateNewsArticle extends CreateRecord
{
    use Translatable;

    protected static string $resource = NewsArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AIHelper::getAutoFillAction('news'),
        ];
    }
}
