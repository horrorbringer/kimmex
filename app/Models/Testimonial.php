<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasTranslations;

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
