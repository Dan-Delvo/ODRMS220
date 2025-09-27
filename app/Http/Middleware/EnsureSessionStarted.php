<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionStarted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force session to start
        $request->session()->start();

        // Ensure CSRF token exists
        if (!$request->session()->has('_token')) {
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}
