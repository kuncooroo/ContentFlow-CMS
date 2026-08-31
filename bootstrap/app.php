<?php

use App\Http\Middleware\EnsureNotDemoRestricted;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')
                ->group(base_path('routes/install.php'));

            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
        $middleware->appendToGroup('web', SecurityHeaders::class);
        $middleware->appendToGroup('web', EnsureNotDemoRestricted::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
