<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../routes/turbo.php', __DIR__.'/../routes/web.php', __DIR__.'/../routes/auth.php'],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {},
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \Tonysm\TailwindCss\Http\Middleware\AddLinkHeaderForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'single.user' => \App\Http\Middleware\SingleUserRestriction::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
