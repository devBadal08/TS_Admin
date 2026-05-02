<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Portfolio extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'gallery',
        'url',
    ];

    protected $casts = [
        'gallery' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // DELETE (remove files)
        static::deleting(function ($portfolio) {

            // Delete main image
            if ($portfolio->image) {
                Storage::disk('public')->delete($portfolio->image);
            }

            // Delete gallery images
            if ($portfolio->gallery) {
                foreach ($portfolio->gallery as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
        });

        // UPDATE (remove old files if changed)
        static::updating(function ($portfolio) {

            // If main image changed
            if ($portfolio->isDirty('image')) {
                Storage::disk('public')->delete($portfolio->getOriginal('image'));
            }

            // If gallery changed
            if ($portfolio->isDirty('gallery')) {
                foreach ($portfolio->getOriginal('gallery') ?? [] as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
        });
    }
}