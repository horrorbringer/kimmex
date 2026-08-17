<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Support\AIHelper;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AIHelper::getAutoFillAction('document'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $titleEn = $data['title_en'] ?? '';
        $titleKm = ! empty($data['title_km']) ? $data['title_km'] : '';

        $descEn = $data['description_en'] ?? '';
        $descKm = ! empty($data['description_km']) ? $data['description_km'] : '';

        $data['title'] = [
            'en' => $titleEn,
            'km' => $titleKm,
        ];

        $data['description'] = [
            'en' => $descEn,
            'km' => $descKm,
        ];

        // Handle External File URL vs Upload
        if (($data['fileUrl_source'] ?? 'upload') === 'url') {
            $data['fileUrl'] = $data['fileUrl_external'] ?? null;
            if (empty($data['fileType']) && ! empty($data['fileUrl'])) {
                $ext = strtoupper(pathinfo(parse_url($data['fileUrl'], PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                $data['fileType'] = $ext ?: 'LINK';
            }
        }

        // Handle External Thumbnail URL vs Upload
        if (($data['thumbnailUrl_source'] ?? 'upload') === 'url') {
            $data['thumbnailUrl'] = $data['thumbnailUrl_external'] ?? null;
        }

        unset(
            $data['title_en'],
            $data['title_km'],
            $data['description_en'],
            $data['description_km'],
            $data['fileUrl_source'],
            $data['fileUrl_external'],
            $data['thumbnailUrl_source'],
            $data['thumbnailUrl_external'],
            $data['_slug_manual']
        );

        return $data;
    }
}
