<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

class Employee extends Model
{
    use DeletesPublicUploads, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'image',
        'experience',
        'location',
        'specialization',
        'role',
        'user_id',
        'isActive',
    ];

    protected array $publicUploadAttributes = ['image'];

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
        Cache::forget('about_orgchart_en');
        Cache::forget('about_orgchart_kh');
        Cache::forget('about_orgchart_km');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orgUnit(): HasOne
    {
        return $this->hasOne(OrgUnit::class, 'employeeId');
    }
}
