import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
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
                'resources/css/admin/dashboard.css',
                'resources/css/admin/guest-list.css',
                'resources/css/admin/photos.css',
                'resources/css/admin/import.css',
                'resources/css/admin/create.css',
                'resources/css/admin/login.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
