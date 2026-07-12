<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TrackPageView::class,
        ]);
        // Global rate limit: 120 requests/minute per IP (prevents DDoS)
        $middleware->web(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':global',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('Too many attempts. Please wait a moment and try again.')], 429);
            }
            return redirect()->back()->with('error', __('Too many attempts. Please wait a moment and try again.'));
        });
    })->create();

    if (($_SERVER['SERVER_NAME'] ?? '') === 'www.kimmex.com.kh' || ($_SERVER['SERVER_NAME'] ?? '') === 'kimmex.com.kh') {
        $app->usePublicPath(dirname($app->basePath()).'/public_html');
    }
return $app;