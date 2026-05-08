<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Portfolio;
use App\Models\Blog; 
use App\Models\Contact;
use App\Models\Client;
use App\Models\Gallery;
use App\Models\PageVisit;

Route::post('/track-page-visit', function (Request $request) {

    PageVisit::create([
        'page' => $request->page,
        'session_id' => $request->session_id,
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'last_active' => now(),
    ]);

    return response()->json([
        'success' => true
    ]);
}); 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/admin', function (Request $request) {
    return response()->json(['message' => 'Welcome to the admin area!']);
})->name('admin.dashboard');

Route::get('/portfolios', function () {
    return Portfolio::orderBy('created_at', 'desc')->get();
});

Route::get('/clients', function () {
    return Client::orderBy('created_at','desc')->get()->map(function ($client) {
        return [
            'id' => $client->id,
            'logo' => $client->logo ? asset('storage/' . $client->logo) : null,
        ];
    });
});

Route::get('/galleries', function () {
    return Gallery::orderBy('created_at', 'desc')->get()->map(function ($gallery) {
        return [
            'id' => $gallery->id,
            'title' => $gallery->title,
            'main_image' => $gallery->main_image
                ? asset('storage/' . $gallery->main_image)
                : null,
        ];
    });
});

Route::get('/galleries/{id}', function ($id) {

    $gallery = Gallery::findOrFail($id);

    return [
        'id' => $gallery->id,
        'title' => $gallery->title,
        'description' => $gallery->description,
        'main_image' => asset('storage/' . $gallery->main_image),
        'gallery_images' => collect($gallery->gallery_images)->map(function ($img) {
            return asset('storage/' . $img);
        }),
    ];
});

Route::post('/contact', function (Request $request) {
    $data = $request->all(); // get all input

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