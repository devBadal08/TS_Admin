<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Portfolio;
use App\Models\Blog; 
use App\Models\Contact;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/admin', function (Request $request) {
    return response()->json(['message' => 'Welcome to the admin area!']);
})->name('admin.dashboard');

Route::get('/portfolios', function () {
    return Portfolio::all();
});
Route::post('/contact', function (Request $request) {
    $data = $request->all(); // get all input

    // Log the request
    \Log::info('Contact Form Data:', $data);

    // Validate
    $validated = validator($data, [
        'name'      => 'required|string|max:255',
        'mobileno'  => 'required|string|max:20',
        'email'     => 'required|email|max:255',
        'subject'   => 'nullable|string|max:255',
        'message'   => 'required|string',
    ])->validate();

    \App\Models\Contact::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Contact form submitted successfully!',
    ]);
});



// BLOG LIST ENDPOINT
Route::get('/blogs', function () {
    return Blog::orderBy('created_at', 'desc')->get()->map(function ($blog) {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'paragraph' => $blog->paragraph,
            'image' => $blog->image ? asset('storage/' . $blog->image) : null,
            'content' => $blog->content,
            'gallery' => $blog->gallery ?? [],
            'tags' => $blog->tags ?? [],
            'created_at' => $blog->created_at, // ✅ Include this
        ];
    });
});

// SINGLE BLOG BY SLUG ENDPOINT
Route::get('/blogs/{slug}', function ($slug) {
    $blog = Blog::where('slug', $slug)->firstOrFail();
    return [
        'id' => $blog->id,
        'title' => $blog->title,
        'slug' => $blog->slug,
        'paragraph' => $blog->paragraph,
        'image' => $blog->image ? asset('storage/' . $blog->image) : null,
        'content' => $blog->content,
        'gallery' => $blog->gallery ?? [],
        'tags' => $blog->tags ?? [],
    ];
});