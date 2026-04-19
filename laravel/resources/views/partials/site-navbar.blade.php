@php
    $titleForBrand = $brandTitle ?? config('wedding.event.title', config('app.name'));
    $brand = __($titleForBrand);
    $monogram = trim((string) config('wedding.event.monogram', ''));
    if ($monogram === '') {
        $words = preg_split('/\s+/u', trim((string) $titleForBrand)) ?: [];
        foreach (array_slice($words, 0, 2) as $w) {
            if ($w !== '') {
                $monogram .= mb_strtoupper(mb_substr($w, 0, 1));
            }
        }
    }
    if ($monogram === '') {
        $monogram = '♥';
    }
    $isHome = request()->routeIs('home', 'wedding.show', 'wedding.enter');
    $isAttend = request()->routeIs('wedding.attend');
    $isGallery = request()->routeIs('gallery.show');
    $isAlbum = request()->routeIs('gallery.album');
    $isRegistry = request()->routeIs('registry.show');
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
                    class="site-header__pill {{ $isAttend ? 'is-active' : '' }}"
                    href="{{ route('wedding.attend') }}"
                    @if ($isAttend) aria-current="page" @endif
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ __('Attend') }}
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
            <li class="site-header__nav-li site-header__nav-li--registry">
                <a
                    class="site-header__pill site-header__pill--registry {{ $isRegistry ? 'is-active' : '' }}"
                    href="{{ route('registry.show') }}"
                    @if ($isRegistry) aria-current="page" @endif
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12v10H4V12"/><path d="M22 7H2v5h20z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                    {{ __('Gift list') }}
                </a>
            </li>
        </ul>
    </nav>
</header>
