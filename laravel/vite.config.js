import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

function tunnelServerConfig(env) {
    const tunnelOrigin = env.VITE_DEV_TUNNEL_URL?.replace(/\/$/, '');

    if (!tunnelOrigin) {
        return {};
    }

    const url = new URL(tunnelOrigin);
    const isHttps = url.protocol === 'https:';
    const appOrigin = env.APP_URL?.replace(/\/$/, '');
    const corsOrigins = [tunnelOrigin, appOrigin].filter(Boolean);

    return {
        host: '0.0.0.0',
        strictPort: true,
        origin: tunnelOrigin,
        cors: corsOrigins.length > 0 ? { origin: corsOrigins } : true,
        hmr: {
            protocol: isHttps ? 'wss' : 'ws',
            host: url.hostname,
            clientPort: url.port ? Number(url.port) : (isHttps ? 443 : 80),
        },
    };
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/site/wedding.css',
                'resources/css/site/gallery.css',
                'resources/css/site/gallery-album.css',
                'resources/js/site/turbo-public.js',
                'resources/js/site/wedding.js',
                'resources/js/site/gallery.js',
                'resources/js/site/gallery-album.js',
                'resources/css/site/registry.css',
                'resources/js/site/registry.js',
                'resources/css/admin/dashboard.css',
                'resources/css/admin/guest-list.css',
                'resources/css/admin/photos.css',
                'resources/css/admin/import.css',
                'resources/css/admin/create.css',
                'resources/css/admin/login.css',
                'resources/css/admin/registry.css',
                'resources/css/admin/seating.css',
                'resources/css/admin/audit.css',
                'resources/css/admin/songs.css',
                'resources/css/admin/gallery-qr.css',
            ],
            refresh: true,
        }),
            tailwindcss(),
        ],
        server: {
            ...tunnelServerConfig(env),
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
