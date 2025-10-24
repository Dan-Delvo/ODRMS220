<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckLockout
{
    /**
     * Routes that should be excluded from lockout check
     */
    protected $excludedRoutes = [
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Skip lockout check for excluded routes
        if ($this->shouldExclude($request)) {
            return $next($request);
        }

        $ip = $request->ip();
        $lockKey = "locked:$ip";

        if (Cache::has($lockKey)) {
            $lockedUntil = Cache::get($lockKey);
            $remainingMinutes = ceil(($lockedUntil - time()) / 60);
            $remainingSeconds = $lockedUntil - time();

            // Return lockout page - blocking all access
            $response = response()->view('auth.lockout', [
                'remaining_minutes' => $remainingMinutes,
                'remaining_seconds' => $remainingSeconds,
                'locked_until' => $lockedUntil
            ], 403);

            // Add headers to prevent caching and back button access
            return $this->addNoCacheHeaders($response);
        }

        // Add no-cache headers to all responses to prevent back button issues
        $response = $next($request);
        return $this->addNoCacheHeaders($response);
    }

    /**
     * Determine if the request should be excluded from lockout
     */
    protected function shouldExclude(Request $request): bool
    {
        foreach ($this->excludedRoutes as $route) {
            if ($request->is($route) || $request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add no-cache headers to prevent browser caching
     */
    protected function addNoCacheHeaders($response)
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
