<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ActivityLogger;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->append(SecurityHeaders::class);
        $middleware->append(ActivityLogger::class);

        $middleware->alias([
            'auth.seller'   => \App\Http\Middleware\AuthenticateSeller::class,
            'auth.admin'    => \App\Http\Middleware\AuthenticateAdmin::class,
            'guest.seller'  => \App\Http\Middleware\RedirectIfSellerAuthenticated::class,
            'guest.admin'   => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();