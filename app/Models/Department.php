<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Department extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function orgUnits(): HasMany
    {
        return $this->hasMany(OrgUnit::class, 'departmentId');
    }

    public function headUnit(): HasOne
    {
        return $this->hasOne(OrgUnit::class, 'departmentId')
            ->whereIn('type', ['DIRECTOR', 'MANAGER', 'MANAGEMENT'])
            ->orderBy('orderIndex')
            ->with('employee');
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'departmentId');
    }

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("careers_jobs_data_{$locale}");
            }
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("careers_jobs_data_{$locale}");
            }
        });
    }
}
