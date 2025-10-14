<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordFlow
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Illuminate\Http\Response|\Illuminate\Http\JsonResponse $response */
        $response = $next($request);

        // Ensure it's a proper Laravel Response before adding headers
        if (method_exists($response, 'header')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
