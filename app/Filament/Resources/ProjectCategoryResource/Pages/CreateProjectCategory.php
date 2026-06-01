<?php

namespace App\Filament\Resources\ProjectCategoryResource\Pages;

use App\Filament\Resources\ProjectCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectCategory extends CreateRecord
{
    protected static string $resource = ProjectCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
