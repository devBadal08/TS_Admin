<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    protected $fillable = ['title', 'main_image', 'description', 'gallery_images'];

    protected $casts = [
        'gallery_images' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // DELETE (remove files)
        static::deleting(function ($gallery) {

            // Delete main image
            if ($gallery->main_image) {
                Storage::disk('public')->delete($gallery->main_image);
            }

            // Delete gallery images
            if ($gallery->gallery_images) {
                foreach ($gallery->gallery_images as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
        });

        // UPDATE (remove old files if changed)
        static::updating(function ($gallery) {

            // If main image changed
            if ($gallery->isDirty('main_image')) {
                Storage::disk('public')->delete($gallery->getOriginal('main_image'));
            }

            // If gallery images changed
            if ($gallery->isDirty('gallery_images')) {
                foreach ($gallery->getOriginal('gallery_images') ?? [] as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
        });
    }
}
