<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware alias
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class, 
            'session.timeout' => \App\Http\Middleware\CheckSessionTimeout::class,
            'admin' => \App\Http\Middleware\AdminAuthenticate::class,
            'admin.role' => \App\Http\Middleware\CheckAdminRole::class,
        ]);

        // ❌ HAPUS ATAU COMMENT 3 BARIS INI:
        // $middleware->web(append: [
        //     \App\Http\Middleware\CheckSessionTimeout::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle exceptions here
    })->create();
