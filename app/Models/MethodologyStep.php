<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Translatable\HasTranslations;

class MethodologyStep extends Model
{
    use HasUuids, HasTranslations;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'icon',
        'orderIndex',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];
}
