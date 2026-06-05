<?php

namespace App\Http\Middleware;

use App\Services\BrowserLocaleResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(private readonly BrowserLocaleResolver $browserLocale) {}

    public function handle(Request $request, Closure $next): Response
    {
        $allowed = array_keys(config('wedding.locales', []));
        $fallback = $allowed[0] ?? (string) config('app.locale', 'en');

        if ($request->session()->has('locale')) {
            $locale = (string) $request->session()->get('locale');
        } else {
            $locale = $this->browserLocale->resolve($request, $allowed) ?? $fallback;
            $request->session()->put('locale', $locale);
        }

        if (! in_array($locale, $allowed, true)) {
            $locale = $fallback;
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
