<?php

namespace Tests\Unit;

use App\Services\GalleryImageCompressor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryImageCompressorTest extends TestCase
{
    public function test_compress_outputs_jpeg_under_max_dimension_us11(): void
    {
        Storage::fake('public');
        Config::set('gallery.compression.enabled', true);
        Config::set('gallery.compression.max_dimension', 800);
        Config::set('gallery.compression.jpeg_quality', 85);

        $upload = UploadedFile::fake()->image('big.jpg', 1600, 1200);

        $path = (new GalleryImageCompressor)->compressAndStore($upload, 'public');

        $this->assertStringEndsWith('.jpg', $path);
        $this->assertTrue(Storage::disk('public')->exists($path));

        $binary = Storage::disk('public')->get($path);
        $this->assertStringStartsWith("\xff\xd8\xff", $binary);
    }

    public function test_compression_disabled_stores_original_file(): void
    {
        Storage::fake('public');
        Config::set('gallery.compression.enabled', false);

        $upload = UploadedFile::fake()->image('keep.png', 100, 100);

        $path = (new GalleryImageCompressor)->compressAndStore($upload, 'public');

        $this->assertTrue(Storage::disk('public')->exists($path));
        $this->assertStringContainsString('gallery/', $path);
    }
}
