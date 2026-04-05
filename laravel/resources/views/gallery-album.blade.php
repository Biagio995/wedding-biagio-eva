<x-layouts.site-public
    :page-title="__('Shared photos') . ' — ' . config('app.name', 'Wedding')"
    page="gallery-album"
>
    <div class="wrap">
        <h1>{{ __('Shared photos') }}</h1>
        <p class="sub">{{ __('Moments from the day — scroll to load more.') }}</p>

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
            data-download-label="{{ __('Download photo') }}"
            data-save-label="{{ __('Save') }}"
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
</x-layouts.site-public>
