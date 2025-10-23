<?php
header_remove('X-Powered-By');
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminUserMiddleware;
use App\Http\Middleware\CheckLockout;
use App\Http\Middleware\ForgotPasswordFlow;
use App\Http\Middleware\StudentUserMiddleware;
use App\Http\Middleware\EnsureSessionStarted;
use App\Http\Middleware\SanitizeRequestBody;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) { //dito lalagay mga middleware
        //
        $middleware->alias(([
            'useradmin' => AdminUserMiddleware::class,   // Alias for admin middleware
            'userstudent' => StudentUserMiddleware::class,  // Alias for student middleware
            'forgotpassword' => ForgotPasswordFlow::class, // Alias for forgot password middleware
            'lockout' => CheckLockout::class,
            'sanitize' => SanitizeRequestBody::class,
        ]));
        // apply globally
        $middleware->append(CheckLockout::class);
        $middleware->append(SanitizeRequestBody::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
