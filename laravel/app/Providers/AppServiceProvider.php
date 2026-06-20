<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env('VERCEL')) {
            $this->configureForVercel();
        }
    }

    /**
     * Vercel dashboard env vars override vercel.json. Force serverless-safe defaults
     * so a copied local .env (sqlite, session/cache database) does not 500 web routes.
     */
    private function configureForVercel(): void
    {
        $databaseUrl = env('DB_URL') ?: env('DATABASE_URL') ?: env('POSTGRES_URL');

        if (is_string($databaseUrl) && $databaseUrl !== '') {
            config([
                'database.default' => 'pgsql',
                'database.connections.pgsql.url' => $databaseUrl,
            ]);
        }

        if (! env('APP_URL') && is_string(env('VERCEL_URL')) && env('VERCEL_URL') !== '') {
            config(['app.url' => 'https://'.env('VERCEL_URL')]);
        }

        config([
            'session.driver' => 'cookie',
            'cache.default' => 'array',
            'queue.default' => 'sync',
        ]);

        $this->configureWritablePublicDisk();
    }

    /** Vercel lambdas cannot write under storage/; use /tmp and serve via Laravel routes. */
    private function configureWritablePublicDisk(): void
    {
        $root = rtrim(sys_get_temp_dir(), '/\\').DIRECTORY_SEPARATOR.'laravel-public';

        foreach (['', 'gallery'] as $subdir) {
            $dir = $subdir === '' ? $root : $root.DIRECTORY_SEPARATOR.$subdir;
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        config([
            'filesystems.disks.public.root' => $root,
            'filesystems.disks.public.url' => rtrim((string) config('app.url'), '/').'/storage',
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = config('app.url');

        if (is_string($appUrl) && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('gallery-uploads', function (Request $request) {
            $perMinute = max(1, (int) config('gallery.upload.rate_limit.max_per_minute', 30));

            return Limit::perMinute($perMinute)->by($request->ip());
        });
    }
}
