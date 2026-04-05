<?php

return [

    /*
    | US-26: gallery upload limits and content allowlist (MIME from file contents, not extension alone).
    */
    'upload' => [
        'max_kilobytes' => max(256, (int) env('GALLERY_UPLOAD_MAX_KB', 10240)),
        'max_files_per_request' => max(1, min(50, (int) env('GALLERY_UPLOAD_MAX_FILES', 20))),
        /*
        | US-27: max POST /gallery upload submissions per IP per minute (named limiter `gallery-uploads`).
        */
        'rate_limit' => [
            'max_per_minute' => max(1, (int) env('GALLERY_UPLOAD_RATE_PER_MINUTE', 30)),
        ],
        'allowed_mimetypes' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/heic',
            'image/heif',
        ],
    ],

    /*
    | US-11: resize + JPEG re-encode to reduce storage while keeping usable quality.
    | HEIC or undecodable files fall back to original upload (see GalleryImageCompressor).
    */
    'compression' => [
        'enabled' => env('GALLERY_IMAGE_COMPRESSION', true),
        'max_dimension' => (int) env('GALLERY_MAX_DIMENSION', 2048),
        'jpeg_quality' => (int) env('GALLERY_JPEG_QUALITY', 85),
    ],

    /*
    | US-13 public album + infinite scroll feed.
    */
    'public_feed' => [
        'only_approved' => env('GALLERY_PUBLIC_ONLY_APPROVED', false),
        'per_page' => max(1, min(50, (int) env('GALLERY_FEED_PER_PAGE', 12))),
    ],

    /*
    | US-30: HTTP caching hints for CDN/browser (JSON feed and immutable photo downloads).
    | For server-side speed, run `php artisan optimize` in production after deploy.
    */
    'http_cache' => [
        'feed_max_age' => max(0, (int) env('GALLERY_FEED_HTTP_MAX_AGE', 30)),
        'download_max_age' => max(0, (int) env('GALLERY_DOWNLOAD_HTTP_MAX_AGE', 86400)),
    ],

];
