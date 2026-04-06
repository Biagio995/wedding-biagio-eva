@props([
    'pageTitle',
    'brandTitle' => null,
    'page' => 'wedding',
])
@php
    $pageAssets = match ($page) {
        'wedding' => ['resources/css/site/wedding.css', 'resources/js/site/wedding.js'],
        'gallery' => ['resources/css/site/gallery.css', 'resources/js/site/gallery.js'],
        'gallery-album' => ['resources/css/site/gallery-album.css', 'resources/js/site/gallery-album.js'],
        'registry' => ['resources/css/site/registry.css', 'resources/js/site/registry.js'],
        default => ['resources/css/site/wedding.css', 'resources/js/site/wedding.js'],
    };
    $viteAssets = array_merge(['resources/js/site/turbo-public.js'], $pageAssets);
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
    @include('partials.site.wedding-music-player')
</body>
</html>
