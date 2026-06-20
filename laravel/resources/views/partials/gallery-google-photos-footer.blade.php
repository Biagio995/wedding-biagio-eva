<aside class="google-photos-footer">
    <p class="google-photos-footer__text">{{ __('Approved photos are also collected in our Google Photos album.') }}</p>
    <a
        class="google-photos-footer__link"
        href="{{ \App\Services\GalleryExternalGallery::sharedAlbumUrl() }}"
        target="_blank"
        rel="noopener noreferrer"
    >
        {{ __('Open shared album on Google Photos') }}
    </a>
</aside>
