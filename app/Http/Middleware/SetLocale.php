<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    protected array $supportedLocales = ['en', 'km', 'kh'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang')
            ?? session('locale')
            ?? config('app.locale', 'km');

        $normalizedLocale = $this->normalizeLocale($locale);

        if (in_array($normalizedLocale, ['en', 'km'])) {
            app()->setLocale($normalizedLocale);

            if (session('locale') !== $normalizedLocale) {
                session(['locale' => $normalizedLocale]);
            }
        }

        return $next($request);
    }

    protected function getBrowserLocale(Request $request): ?string
    {
        $browserLang = substr($request->server('HTTP_ACCEPT_LANGUAGE', ''), 0, 2);

        return $this->normalizeLocale($browserLang);
    }

    protected function normalizeLocale(?string $locale): ?string
    {
        return match ($locale) {
            'kh' => 'km',
            'en', 'km' => $locale,
            default => null,
        };
    }
}
