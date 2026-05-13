<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class JobPosting extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['title', 'location', 'summary', 'requirements', 'benefits', 'experience', 'salary', 'responsibilities'];

    protected $fillable = [
        'title',
        'slug',
        'departmentId',
        'location',
        'type',
        'summary',
        'requirements',
        'benefits',
        'closingDate',
        'experience',
        'salary',
        'responsibilities',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
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
