<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['clientName', 'clientRole', 'content'];

    protected $fillable = [
        'clientName',
        'clientRole',
        'content',
        'image',
        'rating',
        'order_index',
        'is_active',
    ];
}
