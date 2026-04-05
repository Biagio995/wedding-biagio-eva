@props([
    'pageTitle',
    'brandTitle' => null,
    'page' => 'wedding',
])
@php
    $viteAssets = match ($page) {
        'wedding' => ['resources/css/site/wedding.css', 'resources/js/site/wedding.js'],
        'gallery' => ['resources/css/site/gallery.css', 'resources/js/site/gallery.js'],
        'gallery-album' => ['resources/css/site/gallery-album.css', 'resources/js/site/gallery-album.js'],
        default => ['resources/css/site/wedding.css', 'resources/js/site/wedding.js'],
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.site.head-meta', ['viewportFit' => true, 'colorScheme' => 'light'])
    <title>{{ $pageTitle }}</title>
    @vite($viteAssets)
    {{ $head ?? '' }}
</head>
<body>
    @include('partials.site-navbar', ['brandTitle' => $brandTitle])
    {{ $slot }}
</body>
</html>
