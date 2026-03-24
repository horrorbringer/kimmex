<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    protected array $supportedLocales = ['en', 'km'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang')
            ?? session('locale')
            ?? $this->getBrowserLocale($request)
            ?? config('app.locale', 'en');

        if (in_array($locale, $this->supportedLocales)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }

    protected function getBrowserLocale(Request $request): ?string
    {
        $browserLang = substr($request->server('HTTP_ACCEPT_LANGUAGE', ''), 0, 2);

        return in_array($browserLang, $this->supportedLocales) ? $browserLang : null;
    }
}
