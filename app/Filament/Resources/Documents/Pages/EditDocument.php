<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Support\AIHelper;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AIHelper::getAutoFillAction('document'),
            DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['title_en'] = $record->getTranslation('title', 'en', false) ?: ($data['title'] ?? '');
        $data['title_km'] = $record->getTranslation('title', 'km', false) ?: '';

        $data['description_en'] = $record->getTranslation('description', 'en', false) ?: ($data['description'] ?? '');
        $data['description_km'] = $record->getTranslation('description', 'km', false) ?: '';

        // Hydrate fileUrl source
        $fileUrl = (string) ($data['fileUrl'] ?? '');
        if (str_starts_with($fileUrl, 'http://') || str_starts_with($fileUrl, 'https://')) {
            $data['fileUrl_source'] = 'url';
            $data['fileUrl_external'] = $fileUrl;
            $data['fileUrl'] = null;
        } else {
            $data['fileUrl_source'] = 'upload';
            $data['fileUrl_external'] = '';
        }

        // Hydrate thumbnailUrl source
        $thumbnailUrl = (string) ($data['thumbnailUrl'] ?? '');
        if (str_starts_with($thumbnailUrl, 'http://') || str_starts_with($thumbnailUrl, 'https://')) {
            $data['thumbnailUrl_source'] = 'url';
            $data['thumbnailUrl_external'] = $thumbnailUrl;
            $data['thumbnailUrl'] = null;
        } else {
            $data['thumbnailUrl_source'] = 'upload';
            $data['thumbnailUrl_external'] = '';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
