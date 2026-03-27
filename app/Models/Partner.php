<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'logoUrl',
        'website',
        'type',
        'orderIndex',
    ];
}
