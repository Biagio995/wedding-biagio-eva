<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('gallery-uploads', function (Request $request) {
            $perMinute = max(1, (int) config('gallery.upload.rate_limit.max_per_minute', 30));

            return Limit::perMinute($perMinute)->by($request->ip());
        });
    }
}
