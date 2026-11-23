<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;
use Tymon\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        TrustProxies::at('*');
        $middleware->prepend(TrustProxies::class);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // There is no login page: without this, guests without an Accept header get a 500 (Route [login] not defined)
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API clients may omit the Accept header; without this, validation errors redirect (302) instead of returning 422
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*'));

        // Invalid, expired, blacklisted or missing tokens reach /auth/refresh unhandled, since it skips the auth middleware
        $exceptions->render(fn (JWTException $e) => response()->json(['message' => 'Unauthenticated.'], 401));
    })->create();
