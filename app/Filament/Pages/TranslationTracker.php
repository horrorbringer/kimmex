<?php

namespace App\Filament\Pages;

use App\Filament\Resources\NewsArticles\NewsArticleResource;
use App\Filament\Resources\ProjectCategoryResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Jobs\AutoTranslateModel;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TranslationTracker extends Page
{
    protected string $view = 'filament.pages.translation-tracker';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-language';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('Translation Tracker');
    }

    public static function getNavigationSort(): ?int
    {
        return 97;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * Key fields to check for Khmer translations per model.
     */
    protected function getTrackedFields(): array
    {
        return [
            NewsArticle::class => ['title', 'excerpt', 'content'],
            Service::class => ['title', 'summary', 'description'],
            Project::class => ['title', 'description', 'location'],
            ProjectCategory::class => ['name', 'description'],
        ];
    }

    /**
     * Map model class to Filament resource edit URL.
     */
    protected function getEditUrl(string $modelClass, $record): string
    {
        return match ($modelClass) {
            NewsArticle::class => NewsArticleResource::getUrl('edit', ['record' => $record]),
            Service::class => ServiceResource::getUrl('edit', ['record' => $record]),
            Project::class => ProjectResource::getUrl('edit', ['record' => $record]),
            ProjectCategory::class => ProjectCategoryResource::getUrl('edit', ['record' => $record]),
            default => '#',
        };
    }

    /**
     * Friendly model name for display.
     */
    protected function getModelLabel(string $modelClass): string
    {
        return match ($modelClass) {
            NewsArticle::class => 'News Article',
            Service::class => 'Service',
            Project::class => 'Project',
            ProjectCategory::class => 'Project Category',
            default => class_basename($modelClass),
        };
    }

    /**
     * Get the summary statistics.
     */
    public function getStats(): array
    {
        $total = 0;
        $fullyTranslated = 0;
        $partiallyTranslated = 0;
        $notTranslated = 0;

        foreach ($this->getTrackedFields() as $modelClass => $fields) {
            $records = $modelClass::all();

            foreach ($records as $record) {
                $total++;
                $missingCount = 0;

                foreach ($fields as $field) {
                    $translation = $record->getTranslation($field, 'km', false);
                    if (empty($translation) || (is_string($translation) && trim($translation) === '')) {
                        $missingCount++;
                    }
                }

                if ($missingCount === 0) {
                    $fullyTranslated++;
                } elseif ($missingCount < count($fields)) {
                    $partiallyTranslated++;
                } else {
                    $notTranslated++;
                }
            }
        }

        return compact('total', 'fullyTranslated', 'partiallyTranslated', 'notTranslated');
    }

    /**
     * Get all records with missing translations.
     */
    public function getMissingTranslations(): array
    {
        $results = [];

        foreach ($this->getTrackedFields() as $modelClass => $fields) {
            $records = $modelClass::all();

            foreach ($records as $record) {
                $missingFields = [];

                foreach ($fields as $field) {
                    $translation = $record->getTranslation($field, 'km', false);
                    if (empty($translation) || (is_string($translation) && trim($translation) === '')) {
                        $missingFields[] = $field;
                    }
                }

                if (! empty($missingFields)) {
                    $titleField = match ($modelClass) {
                        ProjectCategory::class => 'name',
                        default => 'title',
                    };

                    $results[] = [
                        'modelClass' => $modelClass,
                        'modelLabel' => $this->getModelLabel($modelClass),
                        'recordId' => $record->getKey(),
                        'recordTitle' => $record->getTranslation($titleField, 'en', false) ?: '(untitled)',
                        'missingFields' => $missingFields,
                        'editUrl' => $this->getEditUrl($modelClass, $record),
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Dispatch AutoTranslateModel jobs for all untranslated records.
     */
    public function translateAll(): void
    {
        $dispatched = 0;

        foreach ($this->getTrackedFields() as $modelClass => $fields) {
            $records = $modelClass::all();

            foreach ($records as $record) {
                $missingFields = [];

                foreach ($fields as $field) {
                    $translation = $record->getTranslation($field, 'km', false);
                    if (empty($translation) || (is_string($translation) && trim($translation) === '')) {
                        $missingFields[] = $field;
                    }
                }

                if (! empty($missingFields)) {
                    AutoTranslateModel::dispatch(
                        $modelClass,
                        $record->getKey(),
                        $missingFields,
                    );
                    $dispatched++;
                }
            }
        }

        Notification::make()
            ->title(__('Translation jobs dispatched'))
            ->body(__(':count records queued for auto-translation.', ['count' => $dispatched]))
            ->success()
            ->send();
    }
}
