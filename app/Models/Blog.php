<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory;

    // Add the fields you want to allow for mass assignment:
    protected $fillable = [
        'title',
        'paragraph',
        'content',
        'image',
        'gallery',
        'tags',
        'slug',
        // add any other columns you have
    ];

    protected $casts = [
    'gallery' => 'array',
    'tags' => 'array',
    'slug' => 'string',
];

  protected static function boot()
    {
        parent::boot();

        // CREATE (slug logic)
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);

                $originalSlug = $blog->slug;
                $count = 2;

                while (static::where('slug', $blog->slug)->exists()) {
                    $blog->slug = $originalSlug . '-' . $count++;
                }
            }
        });

        // DELETE (remove files)
        static::deleting(function ($blog) {

            // Delete main image
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }

            // Delete gallery images
            if ($blog->gallery) {
                foreach ($blog->gallery as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
        });

        // UPDATE (remove old files if changed)
        static::updating(function ($blog) {

            // If main image changed
            if ($blog->isDirty('image')) {
                Storage::disk('public')->delete($blog->getOriginal('image'));
            }

            // If gallery changed
            if ($blog->isDirty('gallery')) {
                foreach ($blog->getOriginal('gallery') ?? [] as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
        });
    }
}