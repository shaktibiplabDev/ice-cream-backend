<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add Laravel's CORS middleware to API routes
        $middleware->api(prepend: [
            HandleCors::class,
        ]);

        // Remove CSRF for API routes
        $middleware->remove(PreventRequestForgery::class);

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