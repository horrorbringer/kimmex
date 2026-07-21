<?php

namespace App\Filament\Resources\NewsArticles\Pages;

use App\Filament\Resources\NewsArticles\NewsArticleResource;
use App\Filament\Support\AIHelper;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditNewsArticle extends EditRecord
{
    use Translatable;

    protected static string $resource = NewsArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AIHelper::getAutoFillAction('news'),
            DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
