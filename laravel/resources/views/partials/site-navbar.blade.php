@php
    $brand = $brandTitle ?? config('wedding.event.title', config('app.name'));
    $words = preg_split('/\s+/u', trim((string) $brand)) ?: [];
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
@once
    <style>
        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            margin: calc(-1rem - var(--safe-top)) calc(-1rem - var(--safe-right)) 1.35rem calc(-1rem - var(--safe-left));
            padding: calc(1rem + var(--safe-top)) calc(1rem + var(--safe-right)) 0.85rem calc(1rem + var(--safe-left));
            background: linear-gradient(180deg, rgba(33, 53, 85, 0.94) 0%, rgba(33, 53, 85, 0.82) 100%);
            backdrop-filter: blur(16px) saturate(1.25);
            -webkit-backdrop-filter: blur(16px) saturate(1.25);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }
        .site-header::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(216, 196, 182, 0.45), transparent);
            pointer-events: none;
        }
        .site-header__bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .site-header__brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            min-width: 0;
            text-decoration: none;
            color: var(--text);
            -webkit-tap-highlight-color: transparent;
        }
        .site-header__brand:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 4px;
            border-radius: 10px;
        }
        .site-header__mono {
            flex-shrink: 0;
            display: inline-flex;
            width: 2.4rem;
            height: 2.4rem;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(216, 196, 182, 0.38), rgba(216, 196, 182, 0.1));
            border: 1px solid rgba(216, 196, 182, 0.35);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: var(--accent);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
        }
        .site-header__title {
            font-size: 0.92rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: min(14rem, 46vw);
        }
        .site-header__langs {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.2rem 0.35rem;
            flex-shrink: 0;
        }
        .site-header__langs span[aria-hidden="true"] {
            color: var(--muted);
            font-size: 0.65rem;
            opacity: 0.7;
        }
        .site-header__lang {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            padding: 0.35rem 0.5rem;
            border-radius: 8px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            -webkit-tap-highlight-color: transparent;
            transition: color 0.15s, background 0.15s;
        }
        .site-header__lang:hover {
            color: var(--text);
            background: rgba(255, 255, 255, 0.06);
        }
        .site-header__lang[aria-current="true"] {
            color: var(--accent);
            font-weight: 700;
            background: rgba(216, 196, 182, 0.14);
        }
        .site-header__lang:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
        .site-header__nav {
            margin: 0;
        }
        .site-header__nav ul {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .site-header__pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.5rem 0.95rem;
            border-radius: 999px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            min-height: 44px;
            -webkit-tap-highlight-color: transparent;
            transition: color 0.2s, background 0.2s, border-color 0.2s, transform 0.15s;
        }
        .site-header__pill svg {
            flex-shrink: 0;
            opacity: 0.9;
        }
        .site-header__pill:hover {
            color: var(--text);
            border-color: rgba(216, 196, 182, 0.35);
            background: rgba(255, 255, 255, 0.06);
        }
        .site-header__pill:active {
            transform: scale(0.98);
        }
        @media (prefers-reduced-motion: reduce) {
            .site-header__pill:active { transform: none; }
        }
        .site-header__pill.is-active {
            color: #213555;
            background: linear-gradient(145deg, #F5EFE7, #D8C4B6);
            border-color: transparent;
            font-weight: 600;
            box-shadow: 0 2px 14px rgba(33, 53, 85, 0.35);
        }
        .site-header__pill.is-active svg {
            opacity: 1;
        }
        .site-header__pill:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }
    </style>
@endonce
<header class="site-header" role="banner">
    <div class="site-header__bar">
        <a class="site-header__brand" href="{{ route('home') }}">
            <span class="site-header__mono" aria-hidden="true">{{ $monogram }}</span>
            <span class="site-header__title">{{ $brand }}</span>
        </a>
        <div class="site-header__langs" aria-label="{{ __('Language') }}">
            @foreach (config('wedding.locales', []) as $code => $label)
                <a
                    class="site-header__lang"
                    href="{{ route('locale.switch', ['locale' => $code]) }}"
                    hreflang="{{ $code }}"
                    @if (app()->getLocale() === $code) aria-current="true" @endif
                >{{ $label }}</a>
                @if (! $loop->last)
                    <span aria-hidden="true">·</span>
                @endif
            @endforeach
        </div>
    </div>
    <nav class="site-header__nav" aria-label="{{ __('Main navigation') }}">
        <ul>
            <li>
                <a
                    class="site-header__pill @if ($isHome) is-active @endif"
                    href="{{ route('home') }}"
                    @if ($isHome) aria-current="page" @endif
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    {{ __('Home') }}
                </a>
            </li>
            <li>
                <a
                    class="site-header__pill @if ($isGallery) is-active @endif"
                    href="{{ route('gallery.show') }}"
                    @if ($isGallery) aria-current="page" @endif
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    {{ __('Gallery') }}
                </a>
            </li>
            <li>
                <a
                    class="site-header__pill @if ($isAlbum) is-active @endif"
                    href="{{ route('gallery.album') }}"
                    @if ($isAlbum) aria-current="page" @endif
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    {{ __('Public album') }}
                </a>
            </li>
        </ul>
    </nav>
</header>
