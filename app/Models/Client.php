<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    protected $fillable = ['logo'];

    protected static function booted()
    {
        static::deleting(function ($client) {
            if ($client->logo) {
                Storage::disk('public')->delete($client->logo);
            }
        });
    }
}


