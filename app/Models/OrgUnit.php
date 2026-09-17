<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class OrgUnit extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['title'];

    protected $fillable = [
        'title',
        'type',
        'chart_group',
        'parentId',
        'employeeId',
        'departmentId',
        'orderIndex',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => static::clearOrgCache());
        static::deleted(fn () => static::clearOrgCache());
    }

    public static function defaultChartGroups(): array
    {
        return [
            [
                'key' => 'main',
                'name_en' => 'Organization Structure',
                'name_km' => 'រចនាសម្ព័ន្ធអង្គភាព',
                'card_style' => 'default',
                'is_active' => true,
            ],
            [
                'key' => 'project_teams',
                'name_en' => 'Project Teams',
                'name_km' => 'ក្រុមការងារគម្រោង',
                'card_style' => 'default',
                'is_active' => true,
            ],
            [
                'key' => 'department_heads',
                'name_en' => 'Department Heads',
                'name_km' => 'ប្រធានផ្នែក',
                'card_style' => 'default',
                'is_active' => true,
            ],
        ];
    }

    /**
     * @return array<int, array{key: string, name_en: string, name_km?: string, is_active?: bool}>
     */
    public static function getChartGroups(bool $activeOnly = false): array
    {
        $groups = SystemSetting::get('org_chart_groups', static::defaultChartGroups());
        if (! is_array($groups) || empty($groups)) {
            $groups = static::defaultChartGroups();
        }

        if ($activeOnly) {
            $groups = array_values(array_filter($groups, fn ($g) => (bool) ($g['is_active'] ?? true)));
        }

        return $groups;
    }

    /**
     * @return array<string, string>
     */
    public static function getChartGroupOptions(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $isKhmer = in_array($locale, ['km', 'kh']);
        $options = [];
        foreach (static::getChartGroups() as $group) {
            $key = $group['key'] ?? 'main';
            $name = $isKhmer
                ? (! empty($group['name_km']) ? $group['name_km'] : ($group['name_en'] ?? $key))
                : (! empty($group['name_en']) ? $group['name_en'] : ($group['name_km'] ?? $key));
            $options[$key] = $name ?: ucfirst(str_replace('_', ' ', $key));
        }

        return $options;
    }

    public static function clearOrgCache(): void
    {
        Cache::forget('about_orgchart_en');
        Cache::forget('about_orgchart_kh');
        Cache::forget('about_orgchart_km');
        Cache::forget('about_orgcharts_en');
        Cache::forget('about_orgcharts_kh');
        Cache::forget('about_orgcharts_km');
        Cache::forget('about_page_en');
        Cache::forget('about_page_kh');
        Cache::forget('about_page_km');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class, 'parentId');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OrgUnit::class, 'parentId');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }

    /** @param  Builder<OrgUnit>  $query */
    public function scopeForChart(Builder $query, string $group = 'main'): Builder
    {
        return $query->where('chart_group', $group);
    }

    public function getPath(): string
    {
        $path = [$this->getTranslation('title', app()->getLocale())];
        $parent = $this->parent;
        $seen = [$this->id];
        $depth = 0;

        while ($parent && ! in_array($parent->id, $seen) && $depth < 20) {
            array_unshift($path, $parent->getTranslation('title', app()->getLocale()));
            $seen[] = $parent->id;
            $parent = $parent->parent;
            $depth++;
        }

        return implode(' > ', $path);
    }
}
