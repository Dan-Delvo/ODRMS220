<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionStarted
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only start session if it hasn't been started
        if (!$request->hasSession() || !$request->session()->isStarted()) {
            $request->session()->start();
        }

        return $next($request);
    }
}
