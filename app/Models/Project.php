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
    ];

    protected $casts = [
        'status' => \App\Enums\ProjectStatus::class,
        'completionDate' => 'datetime',
        'isFeatured' => 'boolean',
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
}
