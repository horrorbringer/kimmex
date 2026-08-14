<?php

namespace App\Filament\Resources\NewsArticles\Pages;

use App\Filament\Resources\NewsArticles\NewsArticleResource;
use App\Filament\Support\AIHelper;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsArticle extends CreateRecord
{
    protected static string $resource = NewsArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AIHelper::getAutoFillAction('news'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $titleEn = $data['title_en'] ?? '';
        $titleKm = ! empty($data['title_km']) ? $data['title_km'] : $titleEn;

        $excerptEn = $data['excerpt_en'] ?? '';
        $excerptKm = ! empty($data['excerpt_km']) ? $data['excerpt_km'] : $excerptEn;

        $contentEn = $data['content_en'] ?? '';
        $contentKm = ! empty($data['content_km']) ? $data['content_km'] : $contentEn;

        $authorNameEn = ! empty($data['authorName_en']) ? $data['authorName_en'] : (auth()->user()?->name ?? '');
        $authorNameKm = ! empty($data['authorName_km']) ? $data['authorName_km'] : $authorNameEn;

        $metaTitleEn = ! empty($data['metaTitle_en']) ? $data['metaTitle_en'] : $titleEn;
        $metaTitleKm = ! empty($data['metaTitle_km']) ? $data['metaTitle_km'] : $titleKm;

        $metaDescriptionEn = ! empty($data['metaDescription_en']) ? $data['metaDescription_en'] : $excerptEn;
        $metaDescriptionKm = ! empty($data['metaDescription_km']) ? $data['metaDescription_km'] : $excerptKm;

        $data['title'] = [
            'en' => $titleEn,
            'km' => $titleKm,
        ];

        $data['excerpt'] = [
            'en' => $excerptEn,
            'km' => $excerptKm,
        ];

        $data['content'] = [
            'en' => $contentEn,
            'km' => $contentKm,
        ];

        $data['authorName'] = [
            'en' => $authorNameEn,
            'km' => $authorNameKm,
        ];

        $data['metaTitle'] = [
            'en' => $metaTitleEn,
            'km' => $metaTitleKm,
        ];

        $data['metaDescription'] = [
            'en' => $metaDescriptionEn,
            'km' => $metaDescriptionKm,
        ];

        // ─── Cover Image & Gallery External URL Support ───
        if (($data['coverImage_source'] ?? 'upload') === 'url') {
            $data['coverImage'] = $data['coverImageUrl'] ?? null;
        }

        $uploadedGallery = in_array($data['gallery_source'] ?? 'upload', ['upload', 'both'], true) ? ($data['gallery'] ?? []) : [];
        $externalGallery = in_array($data['gallery_source'] ?? 'upload', ['urls', 'both'], true) ? ($data['galleryUrls'] ?? []) : [];
        $data['gallery'] = array_values(array_filter(array_merge((array) $uploadedGallery, (array) $externalGallery)));

        unset(
            $data['title_en'], $data['title_km'],
            $data['excerpt_en'], $data['excerpt_km'],
            $data['content_en'], $data['content_km'],
            $data['authorName_en'], $data['authorName_km'],
            $data['metaTitle_en'], $data['metaTitle_km'],
            $data['metaDescription_en'], $data['metaDescription_km'],
            $data['coverImage_source'], $data['coverImageUrl'],
            $data['gallery_source'], $data['galleryUrls']
        );

        return $data;
    }
}
