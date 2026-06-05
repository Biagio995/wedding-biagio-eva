<?php

namespace Tests\Unit;

use App\Rules\GalleryUploadFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class GalleryUploadFileTest extends TestCase
{
    public function test_accepts_jpeg_even_when_mime_misreported(): void
    {
        $jpeg = UploadedFile::fake()->image('photo.jpg', 100, 100);
        $this->assertTrue($this->validateFile($jpeg));
    }

    public function test_accepts_heic_bytes_with_jpeg_mime_from_phone(): void
    {
        $heicHeader = "\x00\x00\x00\x18ftypheic\x00\x00\x00\x00";
        $file = UploadedFile::fake()->createWithContent('IMG_0001.jpg', $heicHeader, 'image/jpeg');

        $this->assertTrue($this->validateFile($file));
    }

    public function test_accepts_heic_as_octet_stream(): void
    {
        $heicHeader = "\x00\x00\x00\x18ftypheic\x00\x00\x00\x00";
        $file = UploadedFile::fake()->createWithContent('photo.heic', $heicHeader, 'application/octet-stream');

        $this->assertTrue($this->validateFile($file));
    }

    public function test_rejects_non_image_payload(): void
    {
        $file = UploadedFile::fake()->createWithContent('vacation.jpg', '<?php echo "x";', 'image/jpeg');

        $this->assertFalse($this->validateFile($file));
    }

    private function validateFile(UploadedFile $file): bool
    {
        $validator = Validator::make(
            ['photo' => $file],
            ['photo' => ['file', new GalleryUploadFile]],
        );

        return $validator->passes();
    }
}
