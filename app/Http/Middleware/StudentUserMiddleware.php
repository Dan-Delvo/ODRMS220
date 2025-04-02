<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StudentUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and has the 'student' role
        if (!empty(Auth::check()) && Auth::user()->roles->name === 'student') {
            return $next($request); // Allow access
        }
        else
        {
            return redirect(url(''));
        }
    }
}
