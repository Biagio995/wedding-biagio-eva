@php
    $albumUrl = \App\Services\GalleryExternalGallery::publicUrl();
    $hasLink = \App\Services\GalleryExternalGallery::hasSharedAlbumLink();
@endphp

<section class="google-photos-card" aria-labelledby="google-photos-heading">
    <h1 id="google-photos-heading" class="google-photos-card__title">{{ __('Our wedding photos on Google Photos') }}</h1>
    <p class="google-photos-card__lead">
        {{ __('View and add your photos on Google Photos — free, with no storage limits on our site.') }}
    </p>

    @if($guest ?? null)
        <p class="google-photos-card__welcome" role="status">
            {{ __('Welcome') }}, {{ $guest->name }}
        </p>
    @endif

    <p class="google-photos-card__actions">
        <a class="google-photos-card__cta" href="{{ $albumUrl }}" target="_blank" rel="noopener noreferrer">
            {{ $hasLink ? __('Open shared album') : __('Open Google Photos') }}
        </a>
    </p>

    @unless($hasLink)
        <p class="google-photos-card__setup" role="note">
            {{ __('The shared album link will appear here once the hosts publish it on Google Photos.') }}
        </p>
    @endunless
</section>
