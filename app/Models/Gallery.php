<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['title', 'main_image', 'description', 'gallery_images'];

    protected $casts = [
        'gallery_images' => 'array',
    ];
}
