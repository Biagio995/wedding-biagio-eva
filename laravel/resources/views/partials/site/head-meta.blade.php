@php
    $viewportFit = $viewportFit ?? false;
    $colorScheme = $colorScheme ?? 'light';
@endphp
    <meta charset="utf-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1{{ $viewportFit ? ', viewport-fit=cover' : '' }}">
    @if ($colorScheme !== null && $colorScheme !== '')
        <meta name="color-scheme" content="{{ $colorScheme }}">
    @endif
    <meta name="theme-color" content="#f8f2eb">
