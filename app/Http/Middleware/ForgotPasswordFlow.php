<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordFlow
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $step = session('password_reset_step', 'email'); // default step is 'email'
        $routeName = $request->route()->getName();

        $steps = [
            'forgot' => 'email',
            'forgot.submit' => 'email',
            'verifyotp' => 'otp',
            'verifyotp.submit' => 'otp',
            'newpassword' => 'newpassword',
            'newpassword.submit' => 'newpassword',
        ];

        $requiredStep = $steps[$routeName] ?? null;

        if ($requiredStep === null) {
            return redirect()->route('forgot');
        }

        if ($requiredStep === 'otp' && $step === 'email') {
            return redirect()->route('forgot');
        }

        if ($requiredStep === 'newpassword' && ($step === 'email' || $step === 'otp')) {
            return redirect()->route('verifyotp');
        }

        return $next($request);
    }
}
