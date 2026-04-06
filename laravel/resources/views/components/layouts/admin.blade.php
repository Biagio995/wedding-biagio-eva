@props([
    'pageTitle',
    /** @var string Partial name under partials/admin/css (without .blade.php), e.g. page-dashboard */
    'cssPage',
    'wrap' => true,
])
@php
    $adminCss = match ($cssPage) {
        'page-dashboard' => 'resources/css/admin/dashboard.css',
        'page-guest-list' => 'resources/css/admin/guest-list.css',
        'page-photos' => 'resources/css/admin/photos.css',
        'page-import' => 'resources/css/admin/import.css',
        'page-create' => 'resources/css/admin/create.css',
        'page-login' => 'resources/css/admin/login.css',
        'page-registry' => 'resources/css/admin/registry.css',
        default => 'resources/css/admin/dashboard.css',
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f8f2eb">
    <title>{{ $pageTitle }}</title>
    @vite([$adminCss])
    {{ $head ?? '' }}
</head>
<body>
    @if ($wrap)
        <div class="wrap">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</body>
</html>
