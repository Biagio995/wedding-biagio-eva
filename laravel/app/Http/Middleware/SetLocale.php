<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = array_keys(config('wedding.locales', []));
        $fallback = config('app.locale', 'en');

        $locale = $request->session()->get('locale', $fallback);
        if (! in_array($locale, $allowed, true)) {
            $locale = $fallback;
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
