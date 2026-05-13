<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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

    public function orgUnits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrgUnit::class, 'departmentId');
    }

    public function headUnit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrgUnit::class, 'departmentId')
            ->whereIn('type', ['DIRECTOR', 'MANAGER', 'MANAGEMENT'])
            ->orderBy('orderIndex')
            ->with('employee');
    }

    public function jobPostings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobPosting::class, 'departmentId');
    }

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("careers_jobs_data_{$locale}");
            }
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("careers_jobs_data_{$locale}");
            }
        });
    }
}
