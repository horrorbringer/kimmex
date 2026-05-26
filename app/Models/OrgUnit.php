<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class OrgUnit extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['title'];

    protected $fillable = [
        'title',
        'type',
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

    protected static function clearOrgCache()
    {
        \Illuminate\Support\Facades\Cache::forget('about_orgchart_en');
        \Illuminate\Support\Facades\Cache::forget('about_orgchart_kh');
        \Illuminate\Support\Facades\Cache::forget('about_orgchart_km');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrgUnit::class, 'parentId');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrgUnit::class, 'parentId');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }

    public function getPath(): string
    {
        $path = [$this->getTranslation('title', app()->getLocale())];
        $parent = $this->parent;
        $seen = [$this->id];
        $depth = 0;

        while ($parent && !in_array($parent->id, $seen) && $depth < 20) {
            array_unshift($path, $parent->getTranslation('title', app()->getLocale()));
            $seen[] = $parent->id;
            $parent = $parent->parent;
            $depth++;
        }

        return implode(' > ', $path);
    }
}
