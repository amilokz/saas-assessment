<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'company.status' => \App\Http\Middleware\CheckCompanyStatus::class,
            'trial.expiry' => \App\Http\Middleware\CheckTrialExpiry::class,
            'company.access' => \App\Http\Middleware\EnsureCompanyAccess::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CheckCompanyStatus::class,
            \App\Http\Middleware\CheckTrialExpiry::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();