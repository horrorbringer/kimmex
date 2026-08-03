<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use DeletesPublicUploads, HasTranslations, HasUuids, LogsActivity;

    public $translatable = [
        'title', 'location', 'description',
        'background', 'objectives', 'designConcept',
        'scopeContributions', 'engineeringNarrative',
        'metaTitle', 'metaDescription',
    ];

    protected $fillable = [
        'title',
        'slug',
        'location',
        'heroImage',
        'description',
        'background',
        'objectives',
        'designConcept',
        'scopeContributions',
        'engineeringNarrative',
        'timeline',
        'scale',
        'client',
        'completionDate',
        'category', // Keep for backward compatibility/migration
        'project_category_id',
        'status',
        'isFeatured',
        'isActive',
        'metaTitle',
        'metaDescription',
    ];

    protected array $publicUploadAttributes = ['heroImage'];

    protected $casts = [
        'status' => ProjectStatus::class,
        'completionDate' => 'datetime',
        'isFeatured' => 'boolean',
        'isActive' => 'boolean',
    ];

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class, 'projectId')->orderBy('sort_order');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function newsArticles(): BelongsToMany
    {
        return $this->belongsToMany(NewsArticle::class, 'news_article_project');
    }

    protected static function booted()
    {
        static::saved(function ($project) {
            Cache::forget('projects_all_active');
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("projects_index_data_{$locale}");
                Cache::forget("home_projects_array_{$locale}");
                Cache::forget("home_featured_projects_{$locale}");
                Cache::forget("hero_featured_projects_{$locale}");
                Cache::forget("hero_priority_image_{$locale}");
                Cache::forget("service_featured_projects_{$locale}");
                Cache::forget("nav_project_filters_v1_{$locale}");
                Cache::forget("project_show_data_{$project->slug}_{$locale}");
                Cache::forget("project_categories_active_{$locale}");
            }
        });

        static::deleted(function ($project) {
            Cache::forget('projects_all_active');
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("projects_index_data_{$locale}");
                Cache::forget("home_projects_array_{$locale}");
                Cache::forget("home_featured_projects_{$locale}");
                Cache::forget("hero_featured_projects_{$locale}");
                Cache::forget("hero_priority_image_{$locale}");
                Cache::forget("service_featured_projects_{$locale}");
                Cache::forget("nav_project_filters_v1_{$locale}");
                Cache::forget("project_show_data_{$project->slug}_{$locale}");
                Cache::forget("project_categories_active_{$locale}");
            }
        });
    }
}
