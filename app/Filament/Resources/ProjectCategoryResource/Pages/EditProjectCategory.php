<?php

namespace App\Filament\Resources\ProjectCategoryResource\Pages;

use App\Filament\Resources\ProjectCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectCategory extends EditRecord
{
    protected static string $resource = ProjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['name_en'] = $this->record->getTranslation('name', 'en', false);
        $data['name_km'] = $this->record->getTranslation('name', 'km', false)
            ?: $this->record->getTranslation('name', 'kh', false);
        $data['description_en'] = $this->record->getTranslation('description', 'en', false);
        $data['description_km'] = $this->record->getTranslation('description', 'km', false)
            ?: $this->record->getTranslation('description', 'kh', false);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = [
            'en' => $data['name_en'] ?? '',
            'km' => $data['name_km'] ?? '',
        ];

        $data['description'] = [
            'en' => $data['description_en'] ?? '',
            'km' => $data['description_km'] ?? '',
        ];

        unset($data['name_en'], $data['name_km'], $data['description_en'], $data['description_km']);

        return $data;
    }
}
