<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Employee;
use App\Models\JobPosting;
use App\Models\MethodologyStep;
use App\Models\Milestone;
use App\Models\NewsArticle;
use App\Models\OrgUnit;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Clears relevant frontend caches when content models are created/updated/deleted.
 * This ensures the public website always shows fresh data after admin edits.
 */
class CacheBusterObserver
{
    /** Models that affect the sitemap and should trigger regeneration. */
    protected static array $sitemapModels = [
        Project::class,
        Service::class,
        NewsArticle::class,
        JobPosting::class,
        Document::class,
    ];

    public function saved(Model $model): void
    {
        $this->bustCache($model);
        $this->regenerateSitemap($model);
    }

    public function deleted(Model $model): void
    {
        $this->bustCache($model);
        $this->regenerateSitemap($model);
    }

    protected function regenerateSitemap(Model $model): void
    {
        if (in_array(get_class($model), static::$sitemapModels)) {
            Artisan::call('sitemap:generate');
        }
    }

    protected function bustCache(Model $model): void
    {
        $class = get_class($model);

        // Map model classes to their related cache keys
        $cacheMap = [
            Project::class => [
                'hero_featured_projects_en', 'hero_featured_projects_km',
                'home_projects_array_en', 'home_projects_array_km',
                'nav_categories_en', 'nav_categories_km',
            ],
            Service::class => [
                'home_services_array_v2_en', 'home_services_array_v2_km',
                'nav_services_en', 'nav_services_km',
            ],
            NewsArticle::class => [
                'home_news_array_en', 'home_news_array_km',
                'news_index_data_en', 'news_index_data_km',
                'news_sidebar_documents_en', 'news_sidebar_documents_km',
                'news_sidebar_jobs_en', 'news_sidebar_jobs_km',
            ],
            Partner::class => [
                'home_partners_array_en', 'home_partners_array_km',
            ],
            Testimonial::class => [
                'home_testimonials_array_en', 'home_testimonials_array_km',
            ],
            Milestone::class => [
                'about_milestones_data_en', 'about_milestones_data_km',
                'home_milestones_en', 'home_milestones_km',
                'about_page_en', 'about_page_km',
            ],
            OrgUnit::class => [
                'about_orgchart_en', 'about_orgchart_km',
                'about_page_en', 'about_page_km',
            ],
            Employee::class => [
                'about_orgchart_en', 'about_orgchart_km',
                'about_page_en', 'about_page_km',
            ],
            MethodologyStep::class => [
                'process_index_array_en', 'process_index_array_km',
                'services_process_array_en', 'services_process_array_km',
            ],
            JobPosting::class => [
                'careers_jobs_data_en', 'careers_jobs_data_km',
                'news_sidebar_jobs_en', 'news_sidebar_jobs_km',
            ],
            Document::class => [
                'has_public_documents',
                'document_library_total_documents',
                'document_library_total_categories',
                'document_library_categories_v2_en', 'document_library_categories_v2_km',
                'news_sidebar_documents_en', 'news_sidebar_documents_km',
            ],
            DocumentCategory::class => [
                'document_library_categories_v2_en', 'document_library_categories_v2_km',
                'document_library_total_categories',
            ],
            ProjectCategory::class => [
                'nav_categories_en', 'nav_categories_km',
            ],
            SystemSetting::class => [
                'global_settings_en', 'global_settings_km',
                'about_page_en', 'about_page_km',
            ],
        ];

        $keys = $cacheMap[$class] ?? [];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
