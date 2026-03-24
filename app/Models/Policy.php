<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Policy extends Model
{
    use HasUuids, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'icon',
        'sort_order',
        'is_public',
    ];

    public $translatable = [
        'title',
        'content',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
        'title' => 'json',
        'content' => 'json',
    ];
}
