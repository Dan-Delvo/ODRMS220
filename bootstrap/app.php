<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminUserMiddleware;
use App\Http\Middleware\StudentUserMiddleware;

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
        ]));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
