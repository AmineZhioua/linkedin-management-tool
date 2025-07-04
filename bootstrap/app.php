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
        $middleware->alias([
            'check.subscriptions' => \App\Http\Middleware\CheckValidSubscription::class,
            'linkedin.valid' => \App\Http\Middleware\LinkedinValid::class,
            'linkedin.account.exist' => \App\Http\Middleware\LinkedinAccountExist::class,
            'update.last.activity' => \App\Http\Middleware\UpdateLastActivity::class,
            'check.additional.info' => \App\Http\Middleware\CheckAdditionalInfo::class,
            'check.post.number.kpi' => \App\Http\Middleware\CheckPostNumberForKpis::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'suspend' => \App\Http\Middleware\CheckSuspended::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
