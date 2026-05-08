<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageVisit;

class TrackPageVisits
{
    public function handle(Request $request, Closure $next): Response
    {
        $excludedPaths = [
            'admin',
            'admin/*',

            'filament',
            'filament/*',

            'livewire',
            'livewire/*',

            '_ignition/*',
            'api/*',
        ];

        foreach ($excludedPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }
        
        if ($request->ajax()) {
            return $next($request);
        }

        PageVisit::create([
            'page' => '/' . $request->path(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $next($request);
    }
}