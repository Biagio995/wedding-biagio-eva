<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class PhotoController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    /** US-21: review pending uploads; optional filter status=pending|approved|all. */
    public function index(Request $request): View
    {
        $filter = $request->query('status', 'pending');
        if (! is_string($filter) || ! in_array($filter, ['pending', 'approved', 'all'], true)) {
            $filter = 'pending';
        }

        $query = Photo::query()->with('guest');

        match ($filter) {
            'pending' => $query->where('approved', false),
            'approved' => $query->where('approved', true),
            default => null,
        };

        $photos = $query
            ->latest('id')
            ->simplePaginate(24)
            ->withQueryString();

        return view('admin.photos.index', [
            'photos' => $photos,
            'filter' => $filter,
        ]);
    }

    public function approve(Photo $photo): RedirectResponse
    {
        $photo->update(['approved' => true]);
        $this->audit->log('photo.approved', $photo);

        return redirect()
            ->back()
            ->with('status', __('Photo approved.'));
    }

    /** US-22: remove a photo from storage and the database. */
    public function destroy(Photo $photo): RedirectResponse
    {
        $path = $photo->file_path;
        $this->audit->log('photo.deleted', $photo, ['file_path' => $path]);
        $photo->delete();

        if (is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->back()
            ->with('status', __('Photo removed.'));
    }

    /** US-23: ZIP archive of all stored photo files (skips missing paths). */
    public function downloadArchive(): BinaryFileResponse|RedirectResponse
    {
        if (Photo::query()->doesntExist()) {
            return redirect()
                ->route('admin.photos.index')
                ->with('error', __('No photos to download.'));
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'wedding-gallery-');
        if ($zipPath === false) {
            throw new RuntimeException('Cannot create temporary file.');
        }

        @unlink($zipPath);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Cannot create ZIP archive.');
        }

        $disk = Storage::disk('public');
        $added = 0;

        foreach (Photo::query()->orderBy('id')->cursor() as $photo) {
            if (! $disk->exists($photo->file_path)) {
                continue;
            }
            $contents = $disk->get($photo->file_path);
            $zip->addFromString($this->zipEntryName($photo), $contents);
            $added++;
        }

        $zip->close();

        if ($added === 0) {
            @unlink($zipPath);

            return redirect()
                ->route('admin.photos.index')
                ->with('error', __('No photo files found on disk.'));
        }

        $filename = 'wedding-gallery-'.now()->format('Y-m-d-His').'.zip';

        return response()
            ->download($zipPath, $filename, [
                'Content-Type' => 'application/zip',
            ])
            ->deleteFileAfterSend(true);
    }

    private function zipEntryName(Photo $photo): string
    {
        $base = $photo->original_filename;
        if (! is_string($base) || $base === '') {
            $ext = pathinfo($photo->file_path, PATHINFO_EXTENSION);

            return 'photo-'.$photo->id.($ext !== '' ? '.'.$ext : '');
        }

        $base = basename(str_replace(["\0", '\\'], '', $base));
        if ($base === '') {
            $base = 'photo';
        }

        return $photo->id.'-'.$base;
    }
}
