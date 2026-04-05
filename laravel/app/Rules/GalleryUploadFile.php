<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * US-26: content-aware validation for gallery uploads (MIME sniffing + size limits).
 * HEIC/HEIF from some devices report as application/octet-stream; we allow those when the
 * ISO BMFF brand matches a known HEIC/HEIF family.
 */
class GalleryUploadFile implements ValidationRule
{
    /** @var list<string> */
    private const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'cgi', 'pl', 'asp', 'aspx', 'jsp',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail(__('Invalid upload.'));

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

        $mime = $value->getMimeType();
        $allowed = config('gallery.upload.allowed_mimetypes', []);

        if (is_array($allowed) && in_array($mime, $allowed, true)) {
            if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
                if (! $this->magicBytesMatchRaster($mime, $value)) {
                    $fail(__('Use JPEG, PNG, WebP, GIF, or HEIC images only.'));
                }

                return;
            }

            if (in_array($mime, ['image/heic', 'image/heif'], true)) {
                if (! $this->isLikelyHeicOrHeif($value)) {
                    $fail(__('Use JPEG, PNG, WebP, GIF, or HEIC images only.'));
                }

                return;
            }

            return;
        }

        if ($mime === 'application/octet-stream' && in_array($ext, ['heic', 'heif'], true) && $this->isLikelyHeicOrHeif($value)) {
            return;
        }

        $fail(__('Use JPEG, PNG, WebP, GIF, or HEIC images only.'));
    }

    /**
     * Some stacks guess MIME from the filename; verify actual file header (US-26).
     */
    private function magicBytesMatchRaster(string $mime, UploadedFile $file): bool
    {
        $path = $file->getRealPath();
        if ($path === false || ! is_readable($path)) {
            return false;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $buf = fread($handle, 16);
        fclose($handle);

        if ($buf === false || strlen($buf) < 3) {
            return false;
        }

        return match ($mime) {
            'image/jpeg' => substr($buf, 0, 3) === "\xFF\xD8\xFF",
            'image/png' => strlen($buf) >= 8 && substr($buf, 0, 8) === "\x89PNG\r\n\x1a\n",
            'image/gif' => str_starts_with($buf, 'GIF87a') || str_starts_with($buf, 'GIF89a'),
            'image/webp' => strlen($buf) >= 12 && str_starts_with($buf, 'RIFF') && substr($buf, 8, 4) === 'WEBP',
            default => false,
        };
    }

    private function isLikelyHeicOrHeif(UploadedFile $file): bool
    {
        $path = $file->getRealPath();
        if ($path === false || ! is_readable($path)) {
            return false;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $header = fread($handle, 16);
        fclose($handle);

        if ($header === false || strlen($header) < 12) {
            return false;
        }

        if (substr($header, 4, 4) !== 'ftyp') {
            return false;
        }

        $brand = substr($header, 8, 4);

        return in_array($brand, ['heic', 'heix', 'hevc', 'hevx', 'mif1', 'msf1', 'heim', 'heis', 'avic'], true);
    }
}
