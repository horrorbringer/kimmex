<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasUuids;

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
            static::clearGlobalSettingsCache();
        });

        static::deleted(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
            static::clearGlobalSettingsCache();
        });
    }

    public static function get(string $key, $default = null)
    {
        try {
            $value = Cache::rememberForever("system_setting_{$key}", function () use ($key) {
                $setting = static::where('key', $key)->first();

                return $setting ? $setting->value : null;
            });

            return $value !== null ? $value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function set(string $key, $value)
    {
        $setting = static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("system_setting_{$key}");
        static::clearGlobalSettingsCache();

        return $setting;
    }

    protected static function clearGlobalSettingsCache(): void
    {
        foreach (['en', 'km', 'kh'] as $locale) {
            Cache::forget("global_settings_{$locale}");
        }
    }
}
