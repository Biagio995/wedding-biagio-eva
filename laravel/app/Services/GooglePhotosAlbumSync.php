<?php

namespace App\Services;

use App\Models\Photo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GooglePhotosAlbumSync
{
    public function isConfigured(): bool
    {
        return filled(config('gallery.external.google_photos_client_id'))
            && filled(config('gallery.external.google_photos_client_secret'))
            && filled(config('gallery.external.google_photos_refresh_token'))
            && filled(config('gallery.external.google_photos_album_id'));
    }

    /** Push an approved photo into the shared Google Photos album when API credentials are set. */
    public function pushApprovedPhoto(Photo $photo): bool
    {
        if (! $this->isConfigured() || $photo->google_photos_media_id) {
            return false;
        }

        if (! Storage::disk('public')->exists($photo->file_path)) {
            return false;
        }

        try {
            $accessToken = $this->accessToken();
            $bytes = Storage::disk('public')->get($photo->file_path);
            $mime = Storage::disk('public')->mimeType($photo->file_path) ?: 'image/jpeg';
            $uploadToken = $this->uploadBytes($accessToken, $bytes, $mime);
            $mediaItemId = $this->createMediaItem($accessToken, $uploadToken, $photo);
            $this->addToAlbum($accessToken, $mediaItemId);

            $photo->update([
                'google_photos_media_id' => $mediaItemId,
                'synced_to_google_photos_at' => now(),
            ]);

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    private function accessToken(): string
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('gallery.external.google_photos_client_id'),
            'client_secret' => config('gallery.external.google_photos_client_secret'),
            'refresh_token' => config('gallery.external.google_photos_refresh_token'),
            'grant_type' => 'refresh_token',
        ])->throw();

        $token = $response->json('access_token');

        if (! is_string($token) || $token === '') {
            throw new \RuntimeException('Google Photos access token missing.');
        }

        return $token;
    }

    private function uploadBytes(string $accessToken, string $bytes, string $mime): string
    {
        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => $mime,
                'X-Goog-Upload-Protocol' => 'raw',
                'X-Goog-Upload-File-Name' => 'wedding-photo.jpg',
            ])
            ->withBody($bytes, $mime)
            ->post('https://photoslibrary.googleapis.com/v1/uploads')
            ->throw();

        $uploadToken = trim($response->body());

        if ($uploadToken === '') {
            throw new \RuntimeException('Google Photos upload token missing.');
        }

        return $uploadToken;
    }

    private function createMediaItem(string $accessToken, string $uploadToken, Photo $photo): string
    {
        $filename = $photo->original_filename ?: basename($photo->file_path);

        $response = Http::withToken($accessToken)
            ->post('https://photoslibrary.googleapis.com/v1/mediaItems:batchCreate', [
                'newMediaItems' => [[
                    'description' => is_string($filename) ? $filename : 'Wedding photo',
                    'simpleMediaItem' => [
                        'uploadToken' => $uploadToken,
                    ],
                ]],
            ])
            ->throw();

        $mediaItemId = $response->json('newMediaItemResults.0.mediaItem.id');

        if (! is_string($mediaItemId) || $mediaItemId === '') {
            throw new \RuntimeException('Google Photos media item id missing.');
        }

        return $mediaItemId;
    }

    private function addToAlbum(string $accessToken, string $mediaItemId): void
    {
        $albumId = trim((string) config('gallery.external.google_photos_album_id'));
        if (! str_starts_with($albumId, 'albums/')) {
            $albumId = 'albums/'.$albumId;
        }

        Http::withToken($accessToken)
            ->post("https://photoslibrary.googleapis.com/v1/{$albumId}:batchAddMediaItems", [
                'mediaItemIds' => [$mediaItemId],
            ])
            ->throw();
    }
}
