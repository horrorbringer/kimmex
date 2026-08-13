<?php

namespace App\Filament\Resources\NewsCategories\Pages;

use App\Filament\Resources\NewsCategories\NewsCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNewsCategory extends EditRecord
{
    protected static string $resource = NewsCategoryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locale = app()->getLocale();
        if (isset($data['name']) && is_array($data['name'])) {
            $data['name'] = $data['name'][$locale] ?? $data['name']['en'] ?? reset($data['name']) ?: '';
        }
        if (isset($data['description']) && is_array($data['description'])) {
            $data['description'] = $data['description'][$locale] ?? $data['description']['en'] ?? reset($data['description']) ?: '';
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
