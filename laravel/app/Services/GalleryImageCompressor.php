<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Throwable;

class GalleryImageCompressor
{
    private ?ImageManager $manager = null;

    /**
     * Store a compressed JPEG (max dimension + quality) under public/gallery/.
     * On decode/encode failure, stores the original file unchanged.
     */
    public function compressAndStore(UploadedFile $file, string $disk = 'public'): string
    {
        if (! config('gallery.compression.enabled')) {
            return $this->storeUploadedFile($file, $disk);
        }

        try {
            $image = $this->manager()->decodeSplFileInfo($file);

            if ($image->isAnimated()) {
                $image = $this->useFirstAnimationFrame($image);
            }

            $max = max(256, (int) config('gallery.compression.max_dimension', 2048));
            $image->scaleDown($max, $max);

            $quality = max(50, min(100, (int) config('gallery.compression.jpeg_quality', 85)));
            $encoded = $image->encodeUsingFileExtension('jpg', quality: $quality);

            $relativePath = 'gallery/'.uniqid('c_', true).'.jpg';
            if (! Storage::disk($disk)->put($relativePath, $encoded->toString())) {
                throw new \RuntimeException('Unable to store compressed gallery image.');
            }

            return $relativePath;
        } catch (Throwable $e) {
            report($e);

            return $this->storeUploadedFile($file, $disk);
        }
    }

    private function manager(): ImageManager
    {
        return $this->manager ??= ImageManager::usingDriver(Driver::class);
    }

    private function storeUploadedFile(UploadedFile $file, string $disk): string
    {
        $path = $file->store('gallery', $disk);

        if (! is_string($path) || $path === '') {
            throw new \RuntimeException('Unable to store uploaded gallery image.');
        }

        return $path;
    }

    private function useFirstAnimationFrame(ImageInterface $image): ImageInterface
    {
        $image->removeAnimation(0);

        return $image;
    }
}
