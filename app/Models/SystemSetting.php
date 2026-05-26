<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
            \Illuminate\Support\Facades\Cache::forget("system_setting_{$setting->key}");
        });

        static::deleted(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget("system_setting_{$setting->key}");
        });
    }

    public static function get(string $key, $default = null)
    {
        try {
            $value = \Illuminate\Support\Facades\Cache::rememberForever("system_setting_{$key}", function () use ($key) {
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
        \Illuminate\Support\Facades\Cache::forget("system_setting_{$key}");
        return $setting;
    }
}
