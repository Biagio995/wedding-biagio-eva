<x-layouts.site-public
    :page-title="__('Album') . ' — ' . config('app.name', 'Wedding')"
    page="gallery-album"
>
    <div class="wrap wrap--album">
        @if(\App\Services\GalleryExternalGallery::usesGooglePhotos())
            @include('partials.gallery-google-photos', ['guest' => $guest ?? null])
        @else
            @if(session('upload_success'))
                <div class="flash" role="status" aria-live="polite">
                    {{ __('Photos uploaded. Thank you!') }}
                </div>
            @endif

            @include('partials.gallery-upload')

            <div class="album-grid" id="album-grid" role="list">
                @foreach ($photos as $item)
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

            @if ($photos->hasPages())
                <nav class="album-pagination">
                    {!! $photos->links() !!}
                </nav>
            @endif
        @endif
    </div>
</x-layouts.site-public>
