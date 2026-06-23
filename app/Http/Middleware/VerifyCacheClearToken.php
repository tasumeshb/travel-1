<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCacheClearToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = config('app.cache_clear_token');

        if (empty($token) || $request->query('token') !== $token) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
