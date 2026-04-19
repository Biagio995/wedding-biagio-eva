<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSongRecommendationRequest;
use App\Models\Guest;
use App\Models\SongRecommendation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Public-facing controller for guest song recommendations sent to the DJ.
 *
 * Guests can submit multiple suggestions and delete their own entries (either
 * because they are recognised via session guest, or via a per-session browser
 * token we drop as a cookie on first submission).
 */
class SongRecommendationController extends Controller
{
    public const SESSION_SONG_TOKEN = 'wedding_song_token';

    public function store(StoreSongRecommendationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $guest = $this->currentGuest($request);

        $token = $this->ensureSessionToken($request);

        SongRecommendation::query()->create([
            'guest_id' => $guest?->id,
            'submitted_by' => $guest ? null : ($data['submitted_by'] ?? null),
            'title' => $data['title'],
            'artist' => $data['artist'] ?? null,
            'notes' => $data['notes'] ?? null,
            'session_token' => $token,
        ]);

        return redirect()
            ->route('wedding.show')
            ->with('wedding_success', __('Thanks! Your song suggestion has been added.'))
            ->withFragment('dj-songs');
    }

    public function destroy(Request $request, SongRecommendation $songRecommendation): RedirectResponse
    {
        if (! $this->canManage($request, $songRecommendation)) {
            abort(403);
        }

        $songRecommendation->delete();

        return redirect()
            ->route('wedding.show')
            ->with('wedding_success', __('Song suggestion removed.'))
            ->withFragment('dj-songs');
    }

    /**
     * Helper used by the wedding page to list entries belonging to the current
     * browser/guest, so visitors can remove their own songs without auth.
     *
     * @return \Illuminate\Support\Collection<int, SongRecommendation>
     */
    public static function ownSuggestions(Request $request): \Illuminate\Support\Collection
    {
        $guestId = $request->session()->get(WeddingController::SESSION_WEDDING_GUEST_ID);
        $token = $request->session()->get(self::SESSION_SONG_TOKEN);

        if ($guestId === null && ($token === null || $token === '')) {
            return collect();
        }

        return SongRecommendation::query()
            ->where(function ($q) use ($guestId, $token): void {
                if ($guestId !== null) {
                    $q->where('guest_id', $guestId);
                }
                if (is_string($token) && $token !== '') {
                    $q->orWhere('session_token', $token);
                }
            })
            ->orderByDesc('created_at')
            ->get();
    }

    private function currentGuest(Request $request): ?Guest
    {
        $id = $request->session()->get(WeddingController::SESSION_WEDDING_GUEST_ID);

        return $id ? Guest::query()->find($id) : null;
    }

    private function ensureSessionToken(Request $request): string
    {
        $token = $request->session()->get(self::SESSION_SONG_TOKEN);
        if (! is_string($token) || $token === '') {
            $token = Str::random(32);
            $request->session()->put(self::SESSION_SONG_TOKEN, $token);
        }

        return $token;
    }

    private function canManage(Request $request, SongRecommendation $song): bool
    {
        $guestId = $request->session()->get(WeddingController::SESSION_WEDDING_GUEST_ID);
        if ($guestId !== null && $song->guest_id === (int) $guestId) {
            return true;
        }

        $token = $request->session()->get(self::SESSION_SONG_TOKEN);

        return is_string($token)
            && $token !== ''
            && $song->session_token === $token;
    }
}
