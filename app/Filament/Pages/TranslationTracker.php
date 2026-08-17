<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Departments\DepartmentResource;
use App\Filament\Resources\DocumentCategories\DocumentCategoryResource;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\JobPostings\JobPostingResource;
use App\Filament\Resources\MethodologySteps\MethodologyStepResource;
use App\Filament\Resources\Milestones\MilestoneResource;
use App\Filament\Resources\NewsArticles\NewsArticleResource;
use App\Filament\Resources\NewsCategories\NewsCategoryResource;
use App\Filament\Resources\OrgUnits\OrgUnitResource;
use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\ProjectCategoryResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Sectors\SectorResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Jobs\AutoTranslateModel;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\JobPosting;
use App\Models\MethodologyStep;
use App\Models\Milestone;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\OrgUnit;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Sector;
use App\Models\Service;
use App\Models\Testimonial;
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
            NewsCategory::class => ['name', 'description'],
            Milestone::class => ['title', 'description', 'detailed_description'],
            Sector::class => ['title', 'description'],
            JobPosting::class => ['title', 'location', 'summary'],
            Department::class => ['name', 'description'],
            OrgUnit::class => ['title'],
            Testimonial::class => ['clientName', 'clientRole', 'content'],
            Partner::class => ['name'],
            Document::class => ['title', 'description'],
            DocumentCategory::class => ['name', 'description'],
            MethodologyStep::class => ['title', 'description'],
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
            NewsCategory::class => NewsCategoryResource::getUrl('edit', ['record' => $record]),
            Milestone::class => MilestoneResource::getUrl('edit', ['record' => $record]),
            Sector::class => SectorResource::getUrl('edit', ['record' => $record]),
            JobPosting::class => JobPostingResource::getUrl('edit', ['record' => $record]),
            Department::class => DepartmentResource::getUrl('edit', ['record' => $record]),
            OrgUnit::class => OrgUnitResource::getUrl('edit', ['record' => $record]),
            Testimonial::class => TestimonialResource::getUrl('edit', ['record' => $record]),
            Partner::class => PartnerResource::getUrl('edit', ['record' => $record]),
            Document::class => DocumentResource::getUrl('edit', ['record' => $record]),
            DocumentCategory::class => DocumentCategoryResource::getUrl('edit', ['record' => $record]),
            MethodologyStep::class => MethodologyStepResource::getUrl('edit', ['record' => $record]),
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
            NewsCategory::class => 'News Category',
            Milestone::class => 'Milestone',
            Sector::class => 'Sector',
            JobPosting::class => 'Job Posting',
            Department::class => 'Department',
            OrgUnit::class => 'Org Unit',
            Testimonial::class => 'Testimonial',
            Partner::class => 'Partner',
            Document::class => 'Document',
            DocumentCategory::class => 'Document Category',
            MethodologyStep::class => 'Methodology Step',
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
                        ProjectCategory::class, NewsCategory::class, Department::class, Partner::class, DocumentCategory::class => 'name',
                        Testimonial::class => 'clientName',
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
