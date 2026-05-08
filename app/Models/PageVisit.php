<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = [
        'page',
        'session_id',
        'ip_address',
        'user_agent',
        'last_active',
    ];
}