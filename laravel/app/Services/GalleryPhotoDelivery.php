<?php

namespace App\Services;

use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GalleryPhotoDelivery
{
    public function inline(Photo $photo): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($photo->file_path), 404);

        $filename = $this->filename($photo);
        $mime = Storage::disk('public')->mimeType($photo->file_path) ?: 'application/octet-stream';

        $response = Storage::disk('public')->response(
            $photo->file_path,
            $filename,
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.$this->quotedFilename($filename).'"',
            ],
        );

        return $this->withCacheHeaders($response, 'view');
    }

    public function download(Photo $photo): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($photo->file_path), 404);

        $response = Storage::disk('public')->download(
            $photo->file_path,
            $this->filename($photo),
        );

        return $this->withCacheHeaders($response, 'download');
    }

    private function filename(Photo $photo): string
    {
        $original = $photo->original_filename;
        if (is_string($original) && $original !== '') {
            $base = basename(str_replace(['\\', "\0"], '', $original));
            if ($base !== '') {
                return $base;
            }
        }

        $ext = pathinfo($photo->file_path, PATHINFO_EXTENSION);

        return 'photo-'.$photo->id.($ext !== '' ? '.'.$ext : '');
    }

    private function quotedFilename(string $filename): string
    {
        return str_replace('"', '\\"', $filename);
    }

    private function withCacheHeaders(StreamedResponse $response, string $kind): StreamedResponse
    {
        $key = $kind === 'download' ? 'download_max_age' : 'view_max_age';
        $default = $kind === 'download' ? 86400 : 3600;
        $maxAge = max(0, (int) config('gallery.http_cache.'.$key, $default));

        if ($maxAge > 0) {
            $response->setPublic();
            $response->setMaxAge($maxAge);
            $response->headers->set('Cache-Control', 'public, max-age='.$maxAge.', immutable');
        }

        return $response;
    }
}
