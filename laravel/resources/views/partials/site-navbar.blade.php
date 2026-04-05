@php
    $titleForBrand = $brandTitle ?? config('wedding.event.title', config('app.name'));
    $brand = __($titleForBrand);
    $words = preg_split('/\s+/u', trim((string) $titleForBrand)) ?: [];
    $monogram = '';
    foreach (array_slice($words, 0, 2) as $w) {
        if ($w !== '') {
            $monogram .= mb_strtoupper(mb_substr($w, 0, 1));
        }
    }
    if ($monogram === '') {
        $monogram = '♥';
    }
    $isHome = request()->routeIs('home', 'wedding.show', 'wedding.enter');
    $isGallery = request()->routeIs('gallery.show');
    $isAlbum = request()->routeIs('gallery.album');
@endphp
<header class="site-header" role="banner">
    <div class="site-header__bar">
        <a class="site-header__brand" href="{{ route('home') }}">
            <span class="site-header__mono" aria-hidden="true">{{ $monogram }}</span>
            <span class="site-header__title">{{ $brand }}</span>
        </a>
        @include('partials.site.lang-links')
    </div>
    <nav class="site-header__nav" aria-label="{{ __('Site') }}">
        <ul>
            <li>
                <a
                    class="site-header__pill {{ $isHome ? 'is-active' : '' }}"
                    href="{{ route('home') }}"
                    @if ($isHome) aria-current="page" @endif
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    {{ __('Home') }}
                </a>
            </li>
            <li>
                <a
                    class="site-header__pill {{ $isGallery ? 'is-active' : '' }}"
                    href="{{ route('gallery.show') }}"
                    @if ($isGallery) aria-current="page" @endif
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    {{ __('Gallery') }}
                </a>
            </li>
            <li>
                <a
                    class="site-header__pill {{ $isAlbum ? 'is-active' : '' }}"
                    href="{{ route('gallery.album') }}"
                    @if ($isAlbum) aria-current="page" @endif
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ __('Album') }}
                </a>
            </li>
        </ul>
    </nav>
</header>
