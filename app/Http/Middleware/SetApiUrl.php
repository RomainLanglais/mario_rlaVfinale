<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetApiUrl
{
    public function handle(Request $request, Closure $next)
    {
        // Priorité : session > POST (login) > config par défaut
        $url = session('toad_api_url')
            ?? $request->input('api_url')
            ?? config('services.toad.url', 'http://localhost:8180');

        config(['services.toad.url' => rtrim($url, '/')]);

        return $next($request);
    }
}
