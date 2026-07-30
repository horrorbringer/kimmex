<?php

namespace App\Models;

use App\Enums\JobPostingStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
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
        'telegramQr',
        'closingDate',
        'experience',
        'salary',
        'responsibilities',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => JobPostingStatus::class,
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }

    protected static function booted(): void
    {
        static::saved(function (self $job): void {
            $previous = $job->getPrevious();
            $previousSlug = $previous['slug'] ?? null;

            static::clearPublicCaches($job, is_string($previousSlug) ? $previousSlug : null);
        });

        static::deleted(function (self $job): void {
            static::clearPublicCaches($job);
        });
    }

    private static function clearPublicCaches(self $job, ?string $previousSlug = null): void
    {
        foreach (['en', 'km', 'kh'] as $locale) {
            Cache::forget("careers_jobs_data_{$locale}");

            foreach (array_filter([$job->slug, $previousSlug]) as $slug) {
                Cache::forget("career_job_show_data_{$slug}_{$locale}");
            }
        }
    }
}
