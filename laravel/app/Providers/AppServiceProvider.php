<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
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
        } elseif ($this->usesObjectStorage()) {
            $this->configureObjectStoragePublicDisk();
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
            'gallery.compression.enabled' => false,
            'gallery.upload.max_kilobytes' => min(
                max(256, (int) config('gallery.upload.max_kilobytes', 10240)),
                4096
            ),
        ]);

        if ($this->usesObjectStorage()) {
            $this->configureObjectStoragePublicDisk();
        } else {
            $this->configureWritablePublicDisk();
        }
    }

    private function usesObjectStorage(): bool
    {
        return filled(env('AWS_BUCKET'))
            && filled(env('AWS_ACCESS_KEY_ID'))
            && filled(env('AWS_SECRET_ACCESS_KEY'));
    }

    /** S3-compatible object storage (AWS S3, Cloudflare R2, etc.) survives Vercel cold starts. */
    private function configureObjectStoragePublicDisk(): void
    {
        config([
            'filesystems.disks.public' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION', 'auto'),
                'bucket' => env('AWS_BUCKET'),
                'url' => env('AWS_URL'),
                'endpoint' => env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => filter_var(env('AWS_USE_PATH_STYLE_ENDPOINT', false), FILTER_VALIDATE_BOOL),
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ],
        ]);
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
        if (env('VERCEL') || $this->usesObjectStorage()) {
            Storage::forgetDisk('public');
        }

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
