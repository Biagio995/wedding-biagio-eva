<?php

namespace App\Services;

class GalleryExternalGallery
{
    public static function usesGooglePhotos(): bool
    {
        return config('gallery.external.provider') === 'google_photos';
    }

    public static function sharedAlbumUrl(): ?string
    {
        $url = config('gallery.external.url');

        if (! is_string($url) || $url === '') {
            return null;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
    }

    public static function googlePhotosEmail(): string
    {
        return trim((string) config('gallery.external.google_photos_email', ''));
    }

    /** Where guests land when opening the gallery (shared album or Google Photos home). */
    public static function publicUrl(): string
    {
        return self::sharedAlbumUrl() ?? 'https://photos.google.com/';
    }

    public static function hasSharedAlbumLink(): bool
    {
        return self::sharedAlbumUrl() !== null;
    }
}
