<?php

use App\Http\Middleware\GzipResponse;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackPageView;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            GzipResponse::class,
            SetLocale::class,
            TrackPageView::class,
        ]);
        // Global rate limit: 120 requests/minute per IP (prevents DDoS)
        $middleware->web(prepend: [
            ThrottleRequests::class.':global',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (TooManyRequestsHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('Too many attempts. Please wait a moment and try again.')], 429);
            }

            return redirect()->back()->with('error', __('Too many attempts. Please wait a moment and try again.'));
        });
    })->create();

$cpanelPublicHtml = dirname($app->basePath()).'/public_html';
if (is_dir($cpanelPublicHtml) || in_array($_SERVER['SERVER_NAME'] ?? '', ['www.kimmex.com.kh', 'kimmex.com.kh'], true) || in_array($_SERVER['HTTP_HOST'] ?? '', ['www.kimmex.com.kh', 'kimmex.com.kh'], true)) {
    $app->usePublicPath($cpanelPublicHtml);
}

return $app;
