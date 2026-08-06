<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            return $user->dashboard_url ?? '/dashboard';
        });
        $middleware->append(\App\Http\Middleware\ConvertPersianNumbers::class);
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'is_reseller' => \App\Http\Middleware\IsReseller::class,
            'is_customer' => \App\Http\Middleware\IsCustomer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
