<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GstVerifyService
{
    public function verify($gstNo)
    {
        return Cache::remember(
            'gst_'.$gstNo,
            now()->addDays(30),
            function () use ($gstNo) {

                $response = Http::withHeaders([
                    'X-API-Key' => config('services.gstverify.api_key'),
                    'Accept' => 'application/json',
                ])->get(
                    config('services.gstverify.base_url') . '/api/v1/verify/' . $gstNo
                );

                if (!$response->successful()) {
                    return null;
                }

                return $response->json()['data'] ?? null;
            }
        );
    }
}