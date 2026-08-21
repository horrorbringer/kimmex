<?php

namespace App\Models\Concerns;

use App\Support\PublicStorage;
use App\Support\RichContent;

trait DeletesPublicUploads
{
    protected static function bootDeletesPublicUploads(): void
    {
        static::updating(function ($model) {
            $orphaned = $model->findOrphanedUploadPathsOnUpdate();
            if (! empty($orphaned)) {
                PublicStorage::delete($orphaned);
            }
        });

        static::deleted(function ($model) {
            PublicStorage::delete($model->publicUploadPathsForDeletion());
        });
    }

    /**
     * Identify uploaded files that were present in original state but removed in new state.
     */
    protected function findOrphanedUploadPathsOnUpdate(): array
    {
        $orphaned = [];

        // 1. Check direct file/image attributes (e.g. coverImage, gallery)
        foreach ($this->publicUploadAttributes ?? [] as $attribute) {
            if ($this->isDirty($attribute)) {
                $oldValues = (array) $this->getOriginal($attribute);
                $newValues = (array) $this->getAttribute($attribute);
                $removed = array_diff($oldValues, $newValues);
                foreach ($removed as $path) {
                    if (is_string($path) && filled($path)) {
                        $orphaned[] = $path;
                    }
                }
            }
        }

        // 2. Check rich content / HTML attributes (e.g. content, description, body)
        $richAttributes = property_exists($this, 'richContentAttributes')
            ? $this->richContentAttributes
            : ['content', 'description', 'body'];

        foreach ($richAttributes as $attribute) {
            if ($this->isDirty($attribute)) {
                $oldRaw = $this->getOriginal($attribute);
                $newRaw = method_exists($this, 'getTranslations') && in_array($attribute, $this->translatable ?? [], true)
                    ? $this->getTranslations($attribute)
                    : $this->getAttribute($attribute);

                $oldPaths = RichContent::extractImagePaths($oldRaw);
                $newPaths = RichContent::extractImagePaths($newRaw);

                $removedImages = array_diff($oldPaths, $newPaths);
                foreach ($removedImages as $path) {
                    $orphaned[] = $path;
                }
            }
        }

        return array_values(array_unique(array_filter($orphaned)));
    }

    /**
     * Extract all upload paths (file attributes + embedded rich content images) for complete deletion.
     */
    protected function publicUploadPathsForDeletion(): array
    {
        $paths = collect($this->publicUploadAttributes ?? [])
            ->flatMap(fn (string $attribute) => (array) $this->getAttribute($attribute))
            ->filter()
            ->values()
            ->all();

        $richAttributes = property_exists($this, 'richContentAttributes')
            ? $this->richContentAttributes
            : ['content', 'description', 'body'];

        foreach ($richAttributes as $attribute) {
            $content = method_exists($this, 'getTranslations') && in_array($attribute, $this->translatable ?? [], true)
                ? $this->getTranslations($attribute)
                : $this->getAttribute($attribute);

            if ($content) {
                $embedded = RichContent::extractImagePaths($content);
                $paths = array_merge($paths, $embedded);
            }
        }

        return array_values(array_unique(array_filter($paths)));
    }
}
