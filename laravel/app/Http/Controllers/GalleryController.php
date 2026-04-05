<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGalleryPhotosRequest;
use App\Models\Guest;
use App\Models\Photo;
use App\Services\GalleryImageCompressor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Guest photo gallery (US-29: no user accounts — optional recognition via token + `gallery_guest_id` session).
 * US-30: DB indexes on `photos`, HTTP cache headers on feed/download, one fewer guest query when opening via token.
 */
class GalleryController extends Controller
{
    private const SESSION_GUEST_ID = 'gallery_guest_id';

    public function show(Request $request): View|RedirectResponse
    {
        $token = $request->query('token');

        $guest = null;
        if (is_string($token) && $token !== '') {
            $guest = Guest::query()->where('token', $token)->first();
            if ($guest) {
                $request->session()->put(self::SESSION_GUEST_ID, $guest->id);
            }
        }

        if ($guest === null) {
            $guestId = $request->session()->get(self::SESSION_GUEST_ID);
            $guest = $guestId ? Guest::query()->find($guestId) : null;
        }

        return view('gallery', [
            'guest' => $guest,
        ]);
    }

    /** US-13 / US-14: public album (first page SSR + infinite scroll via {@see feed}). */
    public function album(): View
    {
        $perPage = (int) config('gallery.public_feed.per_page');

        $paginator = $this->publicPhotoQuery(null)
            ->latest('id')
            ->paginate($perPage);

        $paginator->setPath(route('gallery.feed'));

        $initialPhotos = $paginator->getCollection()
            ->map(fn (Photo $p) => $this->photoPublicArray($p))
            ->all();

        return view('gallery-album', [
            'initialPhotos' => $initialPhotos,
            'nextPageUrl' => $paginator->nextPageUrl(),
        ]);
    }

    /** US-13 / US-14: JSON page for infinite scroll. */
    public function feed(Request $request): JsonResponse
    {
        $filterDate = $this->resolveGalleryDateFilter($request);
        $perPage = (int) config('gallery.public_feed.per_page');

        $paginator = $this->publicPhotoQuery($filterDate)
            ->latest('id')
            ->paginate($perPage);

        $paginator->setPath(route('gallery.feed'));
        $this->appendDateToPaginator($paginator, $filterDate);

        $maxAge = max(0, (int) config('gallery.http_cache.feed_max_age', 30));
        $headers = [];
        if ($maxAge > 0) {
            $headers['Cache-Control'] = 'public, max-age='.$maxAge;
        }

        return response()->json([
            'data' => $paginator->getCollection()
                ->map(fn (Photo $p) => $this->photoPublicArray($p))
                ->values()
                ->all(),
            'next_page_url' => $paginator->nextPageUrl(),
        ])->withHeaders($headers);
    }

    /** US-15: single-file download for photos visible in the public album (same rules as the album feed). */
    public function download(int $photo): StreamedResponse
    {
        $model = Photo::query()->forPublicFeed()->whereKey($photo)->firstOrFail();

        if (! Storage::disk('public')->exists($model->file_path)) {
            abort(404);
        }

        $response = Storage::disk('public')->download(
            $model->file_path,
            $this->publicDownloadFilename($model),
        );

        $maxAge = max(0, (int) config('gallery.http_cache.download_max_age', 86400));
        if ($maxAge > 0) {
            $response->setPublic();
            $response->setMaxAge($maxAge);
            $response->headers->set('Cache-Control', 'public, max-age='.$maxAge.', immutable');
        }

        return $response;
    }

    private function publicPhotoQuery(?string $filterDate): Builder
    {
        $query = Photo::query()->forPublicFeed();

        if ($filterDate !== null) {
            $query->whereUploadedOnDate($filterDate);
        }

        return $query;
    }

    private function resolveGalleryDateFilter(Request $request): ?string
    {
        $raw = $request->query('date');
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $validator = Validator::make(
            ['date' => $raw],
            ['date' => ['required', 'date_format:Y-m-d']],
        );

        if ($validator->fails()) {
            return null;
        }

        return $raw;
    }

    private function appendDateToPaginator(LengthAwarePaginator $paginator, ?string $filterDate): void
    {
        if ($filterDate !== null) {
            $paginator->appends(['date' => $filterDate]);
        }
    }

    /**
     * @return array{id: int, url: string, alt: string, download_url: string}
     */
    private function photoPublicArray(Photo $p): array
    {
        return [
            'id' => $p->id,
            'url' => Storage::disk('public')->url($p->file_path),
            'alt' => $p->original_filename ?: __('Photo'),
            'download_url' => route('gallery.photo.download', ['photo' => $p->id]),
        ];
    }

    private function publicDownloadFilename(Photo $photo): string
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

    public function uploadAlias(Request $request): RedirectResponse
    {
        $query = $request->getQueryString();

        return redirect()->to('/gallery'.($query ? '?'.$query : ''));
    }

    public function store(StoreGalleryPhotosRequest $request, GalleryImageCompressor $compressor): RedirectResponse|JsonResponse
    {
        $guestId = $request->session()->get(self::SESSION_GUEST_ID);

        foreach ($request->file('photos') as $file) {
            $path = $compressor->compressAndStore($file, 'public');

            /** US-12: trace uploads to the guest resolved from token/session (nullable). */
            Photo::query()->create([
                'guest_id' => $guestId,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'approved' => false,
            ]);
        }

        $request->session()->flash('upload_success', true);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'redirect' => route('gallery.show'),
            ]);
        }

        return redirect()->route('gallery.show');
    }
}
