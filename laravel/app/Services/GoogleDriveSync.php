<?php

namespace App\Services;

use App\Models\Photo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GoogleDriveSync
{
    public function isConfigured(): bool
    {
        return filled(config('gallery.google_drive.apps_script_url'))
            && filled(config('gallery.google_drive.secret'));
    }

    public function folderUrl(): ?string
    {
        $folderId = trim((string) config('gallery.google_drive.folder_id'));

        if ($folderId === '') {
            return null;
        }

        return 'https://drive.google.com/drive/folders/'.$folderId;
    }

    /** Upload an approved photo into Google Drive via Apps Script (no Google Cloud billing). */
    public function pushApprovedPhoto(Photo $photo): bool
    {
        if (! $this->isConfigured() || $photo->google_drive_file_id) {
            return false;
        }

        if (! Storage::disk('public')->exists($photo->file_path)) {
            return false;
        }

        try {
            $bytes = Storage::disk('public')->get($photo->file_path);
            $mime = Storage::disk('public')->mimeType($photo->file_path) ?: 'image/jpeg';
            $filename = $this->filename($photo);

            $response = Http::asJson()
                ->timeout((int) config('gallery.google_drive.timeout_seconds', 120))
                ->post((string) config('gallery.google_drive.apps_script_url'), [
                    'secret' => config('gallery.google_drive.secret'),
                    'filename' => $filename,
                    'mimeType' => $mime,
                    'fileBase64' => base64_encode($bytes),
                    'photoId' => $photo->id,
                ])
                ->throw();

            $data = $response->json();
            if (! is_array($data) || ! ($data['ok'] ?? false)) {
                throw new \RuntimeException(is_string($data['error'] ?? null) ? $data['error'] : 'Google Drive Apps Script upload failed.');
            }

            $fileId = $data['id'] ?? null;
            if (! is_string($fileId) || $fileId === '') {
                throw new \RuntimeException('Google Drive file id missing.');
            }

            $photo->update([
                'google_drive_file_id' => $fileId,
                'synced_to_google_drive_at' => now(),
            ]);

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    private function filename(Photo $photo): string
    {
        $original = $photo->original_filename;
        if (is_string($original) && $original !== '') {
            $base = basename(str_replace(['\\', "\0"], '', $original));
            if ($base !== '') {
                return $photo->id.'-'.$base;
            }
        }

        $ext = pathinfo($photo->file_path, PATHINFO_EXTENSION);

        return 'photo-'.$photo->id.($ext !== '' ? '.'.$ext : '.jpg');
    }
}
