<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SongRecommendation;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SongRecommendationController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): View
    {
        $songs = SongRecommendation::query()
            ->with('guest:id,name')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.songs.index', [
            'songs' => $songs,
        ]);
    }

    public function destroy(SongRecommendation $songRecommendation): RedirectResponse
    {
        $meta = [
            'title' => $songRecommendation->title,
            'artist' => $songRecommendation->artist,
        ];

        $songRecommendation->delete();
        $this->audit->log('song.deleted', $songRecommendation, $meta);

        return redirect()
            ->route('admin.songs.index')
            ->with('admin_success', __('Song suggestion removed.'));
    }

    public function export(): StreamedResponse
    {
        $filename = 'dj-songs-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache',
        ];

        $response = new StreamedResponse(function (): void {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['id', 'submitted_by', 'guest_id', 'guest_name', 'title', 'artist', 'notes', 'created_at'], ',', '"', '\\');

            SongRecommendation::query()
                ->with('guest:id,name')
                ->orderBy('created_at')
                ->chunk(200, function ($chunk) use ($handle): void {
                    foreach ($chunk as $song) {
                        fputcsv($handle, [
                            $song->id,
                            (string) ($song->submitted_by ?? ''),
                            $song->guest_id,
                            (string) ($song->guest->name ?? ''),
                            $song->title,
                            (string) ($song->artist ?? ''),
                            (string) ($song->notes ?? ''),
                            optional($song->created_at)->toIso8601String(),
                        ], ',', '"', '\\');
                    }
                });

            fclose($handle);
        }, 200, $headers);

        $this->audit->log('song.csv.exported');

        return $response;
    }
}
