<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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

    public function localizedName(?string $locale = null): string
    {
        return $this->localizedField('name', $locale) ?: $this->slug;
    }

    public function localizedDescription(?string $locale = null): string
    {
        return $this->localizedField('description', $locale);
    }

    protected function localizedField(string $field, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $fallbackLocales = $locale === 'km' ? ['kh', 'en'] : ($locale === 'kh' ? ['km', 'en'] : ['en']);
        $locales = array_values(array_unique(array_filter(array_merge([$locale], $fallbackLocales))));

        foreach ($locales as $candidateLocale) {
            $value = $this->getTranslation($field, $candidateLocale, false);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("nav_categories_{$locale}");
                Cache::forget("nav_categories_v2_{$locale}");
                Cache::forget("nav_project_filters_v1_{$locale}");
                Cache::forget("projects_index_data_{$locale}");
            }
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("nav_categories_{$locale}");
                Cache::forget("nav_categories_v2_{$locale}");
                Cache::forget("nav_project_filters_v1_{$locale}");
                Cache::forget("projects_index_data_{$locale}");
            }
        });
    }
}
