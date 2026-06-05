<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * US-26: content-aware validation for gallery uploads (MIME sniffing + size limits).
 * Phones often mislabel HEIC/AVIF/WebP as image/jpeg or application/octet-stream.
 */
class GalleryUploadFile implements ValidationRule
{
    /** @var list<string> */
    private const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'cgi', 'pl', 'asp', 'aspx', 'jsp',
    ];

    /** @var array<string, list<string>> */
    private const SNIFF_TO_MIMES = [
        'jpeg' => ['image/jpeg', 'image/jpg', 'image/pjpeg'],
        'png' => ['image/png', 'image/x-png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'heic' => ['image/heic', 'image/heif'],
        'avif' => ['image/avif'],
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail(__('Invalid upload.'));

            return;
        }

        if (! $value->isValid()) {
            $fail($this->uploadErrorMessage($value));

            return;
        }

        $ext = strtolower((string) $value->getClientOriginalExtension());
        if ($ext !== '' && in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
            $fail(__('This file type is not allowed.'));

            return;
        }

        $maxKb = max(1, (int) config('gallery.upload.max_kilobytes', 10240));
        if ($value->getSize() > $maxKb * 1024) {
            $fail(__('Each file must be at most :max kilobytes.', ['max' => $maxKb]));

            return;
        }

        $sniffed = $this->sniffFormat($value);
        if ($sniffed !== null && $this->sniffedFormatAllowed($sniffed)) {
            return;
        }

        $fail(__('Use JPEG, PNG, WebP, GIF, HEIC, or AVIF images only.'));
    }

    private function uploadErrorMessage(UploadedFile $file): string
    {
        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => __('Each file must be at most :max kilobytes.', [
                'max' => max(1, (int) config('gallery.upload.max_kilobytes', 10240)),
            ]),
            UPLOAD_ERR_PARTIAL => __('The upload was interrupted. Please try again.'),
            default => __('Invalid upload.'),
        };
    }

    private function sniffedFormatAllowed(string $format): bool
    {
        $mimes = self::SNIFF_TO_MIMES[$format] ?? [];
        $allowed = config('gallery.upload.allowed_mimetypes', []);

        if (! is_array($allowed)) {
            return false;
        }

        foreach ($mimes as $mime) {
            if (in_array($mime, $allowed, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect the real image format from file bytes (ignores client MIME/extension when wrong).
     */
    private function sniffFormat(UploadedFile $file): ?string
    {
        $path = $file->getRealPath();
        if ($path === false || ! is_readable($path)) {
            return null;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return null;
        }

        $buf = fread($handle, 32);
        fclose($handle);

        if ($buf === false || strlen($buf) < 3) {
            return null;
        }

        if (substr($buf, 0, 3) === "\xFF\xD8\xFF") {
            return 'jpeg';
        }

        if (strlen($buf) >= 8 && substr($buf, 0, 8) === "\x89PNG\r\n\x1a\n") {
            return 'png';
        }

        if (str_starts_with($buf, 'GIF87a') || str_starts_with($buf, 'GIF89a')) {
            return 'gif';
        }

        if (strlen($buf) >= 12 && str_starts_with($buf, 'RIFF') && substr($buf, 8, 4) === 'WEBP') {
            return 'webp';
        }

        if (strlen($buf) >= 12 && substr($buf, 4, 4) === 'ftyp') {
            $brand = substr($buf, 8, 4);

            if (in_array($brand, ['heic', 'heix', 'hevc', 'hevx', 'mif1', 'msf1', 'heim', 'heis', 'avic'], true)) {
                return 'heic';
            }

            if (in_array($brand, ['avif', 'avis'], true)) {
                return 'avif';
            }
        }

        return null;
    }
}
