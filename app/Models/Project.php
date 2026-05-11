<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Spatie\Translatable\HasTranslations;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    use LogsActivity, HasTranslations, HasUuids;

    public $translatable = [
        'title', 'location', 'description',
        'background', 'objectives', 'designConcept',
        'scopeContributions', 'engineeringNarrative'
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
    ];

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
        return $this->hasMany(ProjectImage::class, 'projectId');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('projects_index_data_en');
            \Illuminate\Support\Facades\Cache::forget('projects_index_data_kh');
            \Illuminate\Support\Facades\Cache::forget('home_featured_projects_en');
            \Illuminate\Support\Facades\Cache::forget('home_featured_projects_kh');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('projects_index_data_en');
            \Illuminate\Support\Facades\Cache::forget('projects_index_data_kh');
            \Illuminate\Support\Facades\Cache::forget('home_featured_projects_en');
            \Illuminate\Support\Facades\Cache::forget('home_featured_projects_kh');
        });
    }
}
