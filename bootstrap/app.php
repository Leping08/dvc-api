<?php

use App\Http\Middleware\Domain;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Subdomains of APP_URL, plus this environment's Vapor vanity URL.
        // Keep it to the exact host: a *.vapor-farm-c1.com wildcard would trust
        // other Vapor customers' domains in Host headers.
        $middleware->trustHosts(at: [
            '^misty-garden-8w5lxmxdhmao\.vapor-farm-c1\.com$',
        ]);

        $middleware->alias([
            'dvc_domain' => Domain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
