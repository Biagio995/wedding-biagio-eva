<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#213555">
    <title>{{ __('Shared photos') }} — {{ config('app.name', 'Wedding') }}</title>
    <style>
        :root {
            --bg: #213555;
            --text: #F5EFE7;
            --muted: #b8aea4;
            --accent: #D8C4B6;
            --radius: 14px;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100dvh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(ellipse 120% 80% at 50% -20%, #3E5879 0%, var(--bg) 55%);
            color: var(--text);
            line-height: 1.45;
            padding: calc(1rem + var(--safe-top)) calc(1rem + var(--safe-right)) calc(1.5rem + var(--safe-bottom)) calc(1rem + var(--safe-left));
            overflow-x: hidden;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
            touch-action: manipulation;
        }
        .wrap { max-width: 56rem; margin: 0 auto; }
        h1 {
            font-size: 1.35rem;
            font-weight: 600;
            margin: 0 0 0.35rem;
        }
        .sub { color: var(--muted); font-size: 0.9rem; margin-bottom: 1rem; }
        .album-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(140px, 100%), 1fr));
            gap: 0.5rem;
        }
        @media (min-width: 480px) {
            .album-grid { gap: 0.65rem; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        }
        .album-item {
            margin: 0;
            border-radius: var(--radius);
            overflow: hidden;
            background: rgba(255,255,255,0.04);
            aspect-ratio: 1;
        }
        .album-item-wrap {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .album-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            vertical-align: middle;
        }
        .album-download {
            position: absolute;
            bottom: 0.35rem;
            right: 0.35rem;
            min-width: 2.75rem;
            min-height: 2.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0.55rem;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.2;
            color: #213555;
            background: rgba(245, 239, 231, 0.95);
            border-radius: 6px;
            text-decoration: none;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.35);
            -webkit-tap-highlight-color: transparent;
        }
        .album-download:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
        .album-hint {
            text-align: center;
            font-size: 0.8rem;
            color: var(--muted);
            margin: 1rem 0 0;
        }
        .album-loading {
            text-align: center;
            font-size: 0.85rem;
            color: var(--muted);
            margin: 0.75rem 0;
        }
        .album-loading[hidden] { display: none !important; }
        #album-sentinel {
            height: 1px;
            margin: 0;
        }
        .album-filter {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem 0.75rem;
            margin-bottom: 1rem;
            padding: 0.75rem 0.85rem;
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .album-filter label {
            font-size: 0.85rem;
            color: var(--muted);
        }
        .album-filter input[type="date"] {
            padding: 0.55rem 0.6rem;
            min-height: 44px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(0,0,0,0.25);
            color: var(--text);
            font-size: 1rem;
        }
        .album-filter button {
            padding: 0.55rem 1rem;
            min-height: 44px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(145deg, #F5EFE7, #D8C4B6);
            color: #213555;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        .album-filter button:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
        .album-filter a {
            color: var(--accent);
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            padding: 0.35rem 0.25rem;
            -webkit-tap-highlight-color: transparent;
        }
        .album-filter a:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    @include('partials.site-navbar')
    <div class="wrap">
        <h1>{{ __('Shared photos') }}</h1>
        <p class="sub">{{ __('Moments from the day — scroll to load more.') }}</p>
        <p class="sub" style="margin-top:-0.35rem;font-size:0.85rem;">{{ __('No login needed to browse — anyone with the link can view the album.') }}</p>

        <form class="album-filter" method="get" action="{{ route('gallery.album') }}">
            <label for="filter-date">{{ __('Photos from') }}</label>
            <input
                type="date"
                id="filter-date"
                name="date"
                value="{{ $filterDate ?? '' }}"
                max="{{ now()->format('Y-m-d') }}"
            >
            <button type="submit">{{ __('Apply') }}</button>
            @if (!empty($filterDate))
                <a href="{{ route('gallery.album') }}">{{ __('All dates') }}</a>
            @endif
        </form>

        @if (count($initialPhotos) === 0)
            <p class="album-hint" id="album-empty">
                @if (!empty($filterDate))
                    {{ __('No photos for this date.') }}
                @else
                    {{ __('No photos yet. Upload from the gallery page.') }}
                @endif
            </p>
        @endif

        <div
            id="album-root"
            data-next-url="{{ $nextPageUrl }}"
        >
            <div class="album-grid" id="album-grid" role="list">
                @foreach ($initialPhotos as $item)
                    <figure class="album-item" role="listitem">
                        <div class="album-item-wrap">
                            <img
                                src="{{ $item['url'] }}"
                                alt="{{ $item['alt'] }}"
                                loading="lazy"
                                decoding="async"
                                width="512"
                                height="512"
                            >
                            <a
                                class="album-download"
                                href="{{ $item['download_url'] }}"
                                download
                                aria-label="{{ __('Download photo') }}"
                            >{{ __('Save') }}</a>
                        </div>
                    </figure>
                @endforeach
            </div>
            <p class="album-loading" id="album-loading" hidden>{{ __('Loading more…') }}</p>
            <div id="album-sentinel" aria-hidden="true"></div>
            <p class="album-hint" id="album-end" hidden>{{ __('You’ve reached the end.') }}</p>
        </div>
    </div>
    <script>
        (function () {
            var root = document.getElementById('album-root');
            var grid = document.getElementById('album-grid');
            var sentinel = document.getElementById('album-sentinel');
            var loadingEl = document.getElementById('album-loading');
            var endEl = document.getElementById('album-end');
            if (!root || !grid || !sentinel) return;

            var nextUrl = root.getAttribute('data-next-url') || '';
            var loading = false;

            function appendItems(items) {
                items.forEach(function (item) {
                    var fig = document.createElement('figure');
                    fig.className = 'album-item';
                    fig.setAttribute('role', 'listitem');
                    var wrap = document.createElement('div');
                    wrap.className = 'album-item-wrap';
                    var img = document.createElement('img');
                    img.src = item.url;
                    img.alt = item.alt || '';
                    img.loading = 'lazy';
                    img.decoding = 'async';
                    img.width = 512;
                    img.height = 512;
                    wrap.appendChild(img);
                    if (item.download_url) {
                        var dl = document.createElement('a');
                        dl.className = 'album-download';
                        dl.href = item.download_url;
                        dl.setAttribute('download', '');
                        dl.setAttribute('aria-label', {{ json_encode(__('Download photo')) }});
                        dl.textContent = {{ json_encode(__('Save')) }};
                        wrap.appendChild(dl);
                    }
                    fig.appendChild(wrap);
                    grid.appendChild(fig);
                });
            }

            function loadMore() {
                if (!nextUrl || loading) return;
                loading = true;
                if (loadingEl) loadingEl.hidden = false;
                fetch(nextUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error('feed');
                        return r.json();
                    })
                    .then(function (payload) {
                        appendItems(payload.data || []);
                        nextUrl = payload.next_page_url || '';
                        root.setAttribute('data-next-url', nextUrl || '');
                        if (!nextUrl) {
                            if (endEl) endEl.hidden = false;
                            if (io) io.disconnect();
                        }
                    })
                    .catch(function () {})
                    .finally(function () {
                        loading = false;
                        if (loadingEl) loadingEl.hidden = true;
                    });
            }

            var io = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) loadMore();
                    });
                },
                { rootMargin: '400px' },
            );
            if (nextUrl) {
                io.observe(sentinel);
            } else if (endEl && grid.querySelectorAll('.album-item').length > 0) {
                endEl.hidden = false;
            }
        })();
    </script>
</body>
</html>
