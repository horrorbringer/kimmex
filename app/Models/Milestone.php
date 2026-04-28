<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Translatable\HasTranslations;

class Milestone extends Model
{
    use HasUuids, HasTranslations;

    public $translatable = ['title', 'description', 'detailed_description'];

    protected $fillable = [
        'year',
        'title',
        'description',
        'detailed_description',
        'image',
        'sortOrder',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];
}
