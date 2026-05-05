<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Employee extends Model
{
    use HasUuids;
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

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orgUnit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrgUnit::class, 'employeeId');
    }
}
