<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    // Add the fields you want to allow for mass assignment:
    protected $fillable = [
        'title',
        'paragraph',
        'content',
        'image',
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

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);

                // Ensure slug uniqueness
                $originalSlug = $blog->slug;
                $count = 2;
                while (static::where('slug', $blog->slug)->exists()) {
                    $blog->slug = $originalSlug . '-' . $count++;
                }
            }
        });
}
}