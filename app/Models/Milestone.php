<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Translatable\HasTranslations;

class Milestone extends Model
{
    use HasUuids, HasTranslations;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'year',
        'title',
        'description',
        'image',
        'sortOrder',
    ];
}
