<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Spatie\Translatable\HasTranslations;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    use LogsActivity, HasTranslations, HasUuids, DeletesPublicUploads;

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
        'status' => \App\Enums\ProjectStatus::class,
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

    protected static function booted()
    {
        static::saved(function ($project) {
            \Illuminate\Support\Facades\Cache::forget("projects_all_active");
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("projects_index_data_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("home_projects_array_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("home_featured_projects_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("project_show_data_{$project->slug}_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("project_categories_active_{$locale}");
            }
        });

        static::deleted(function ($project) {
            \Illuminate\Support\Facades\Cache::forget("projects_all_active");
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("projects_index_data_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("home_projects_array_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("home_featured_projects_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("project_show_data_{$project->slug}_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("project_categories_active_{$locale}");
            }
        });
    }
}
