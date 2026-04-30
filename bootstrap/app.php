<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery; // Add this

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // New way to exclude CSRF in Laravel 13
        $middleware->remove(PreventRequestForgery::class);
        // Or you can also use:
        // $middleware->disable(PreventRequestForgery::class);
        
        // Alternative: If you need to keep CSRF but exclude specific routes
        // This requires a different approach - see below
        
        // Redirect unauthenticated users to admin login
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                return route('admin.login');
            }
            return route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();