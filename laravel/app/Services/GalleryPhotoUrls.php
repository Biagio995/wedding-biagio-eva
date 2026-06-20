<?php

namespace App\Services;

use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class GalleryPhotoUrls
{
    public function viewUrl(Photo $photo, bool $admin = false): string
    {
        if ($this->usesDirectObjectUrls()) {
            return Storage::disk('public')->url($photo->file_path);
        }

        return $admin
            ? route('admin.photos.show', ['photo' => $photo->id])
            : route('gallery.photo.show', ['photo' => $photo->id]);
    }

    private function usesDirectObjectUrls(): bool
    {
        return config('filesystems.disks.public.driver') === 's3'
            && filled(config('filesystems.disks.public.url'));
    }
}
