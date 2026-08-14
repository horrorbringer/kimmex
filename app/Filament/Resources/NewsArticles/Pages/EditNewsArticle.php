<?php

namespace App\Filament\Resources\NewsArticles\Pages;

use App\Filament\Resources\NewsArticles\NewsArticleResource;
use App\Filament\Support\AIHelper;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditNewsArticle extends EditRecord
{
    protected static string $resource = NewsArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AIHelper::getAutoFillAction('news'),
            DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['title_en'] = $this->record->getTranslation('title', 'en', false) ?: '';
        $data['title_km'] = $this->record->getTranslation('title', 'km', false) ?: ($this->record->getTranslation('title', 'kh', false) ?: '');

        $data['excerpt_en'] = $this->record->getTranslation('excerpt', 'en', false) ?: '';
        $data['excerpt_km'] = $this->record->getTranslation('excerpt', 'km', false) ?: ($this->record->getTranslation('excerpt', 'kh', false) ?: '');

        $data['content_en'] = $this->record->getTranslation('content', 'en', false) ?: '';
        $data['content_km'] = $this->record->getTranslation('content', 'km', false) ?: ($this->record->getTranslation('content', 'kh', false) ?: '');

        $data['authorName_en'] = $this->record->getTranslation('authorName', 'en', false) ?: '';
        $data['authorName_km'] = $this->record->getTranslation('authorName', 'km', false) ?: ($this->record->getTranslation('authorName', 'kh', false) ?: '');

        $data['metaTitle_en'] = $this->record->getTranslation('metaTitle', 'en', false) ?: '';
        $data['metaTitle_km'] = $this->record->getTranslation('metaTitle', 'km', false) ?: ($this->record->getTranslation('metaTitle', 'kh', false) ?: '');

        $data['metaDescription_en'] = $this->record->getTranslation('metaDescription', 'en', false) ?: '';
        $data['metaDescription_km'] = $this->record->getTranslation('metaDescription', 'km', false) ?: ($this->record->getTranslation('metaDescription', 'kh', false) ?: '');

        // ─── Cover Image Fill ───
        if ($this->record->coverImage && Str::startsWith($this->record->coverImage, ['http://', 'https://'])) {
            $data['coverImage_source'] = 'url';
            $data['coverImageUrl'] = $this->record->coverImage;
            $data['coverImage'] = null;
        } else {
            $data['coverImage_source'] = 'upload';
            $data['coverImageUrl'] = '';
        }

        // ─── Gallery Fill ───
        $localGallery = [];
        $urlGallery = [];
        foreach ($this->record->gallery ?? [] as $img) {
            if (is_string($img) && Str::startsWith($img, ['http://', 'https://'])) {
                $urlGallery[] = $img;
            } elseif (is_string($img) && filled($img)) {
                $localGallery[] = $img;
            }
        }
        $data['gallery'] = $localGallery;
        $data['galleryUrls'] = $urlGallery;
        $data['gallery_source'] = count($urlGallery) > 0 && count($localGallery) > 0 ? 'both' : (count($urlGallery) > 0 ? 'urls' : 'upload');

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
        } else {
            $data['coverImage'] = $data['coverImage'] ?? null;
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
