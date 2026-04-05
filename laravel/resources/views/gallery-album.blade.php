<x-layouts.site-public
    :page-title="__('Shared photos') . ' — ' . config('app.name', 'Wedding')"
    page="gallery-album"
>
    <div class="wrap">
        <h1>{{ __('Shared photos') }}</h1>
        <p class="sub">{{ __('Moments from the day — scroll to load more.') }}</p>

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
