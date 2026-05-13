<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProjectCategory extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'isActive'];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ProjectCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectCategory::class, 'parent_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("nav_categories_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("projects_index_data_{$locale}");
            }
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("nav_categories_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("projects_index_data_{$locale}");
            }
        });
    }
}
