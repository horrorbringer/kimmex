<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasUuids;

    protected $fillable = [
        'email',
        'name',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function booted(): void
    {
        static::creating(function (Subscriber $subscriber) {
            $subscriber->unsubscribe_token ??= bin2hex(random_bytes(32));
            $subscriber->subscribed_at ??= now();
            $subscriber->is_active ??= true;
        });
    }
}
