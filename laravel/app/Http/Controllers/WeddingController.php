<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SongRecommendationController as PublicSongRecommendationController;
use App\Http\Requests\StoreRsvpRequest;
use App\Mail\RsvpAdminNotificationMail;
use App\Mail\RsvpConfirmationMail;
use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Public wedding invitation + RSVP (US-29: no app login — session holds `wedding_guest_id` after token or after first open RSVP).
 */
class WeddingController extends Controller
{
    public const SESSION_WEDDING_GUEST_ID = 'wedding_guest_id';

    public function show(Request $request): View|RedirectResponse
    {
        $queryToken = $request->query('token');
        if (is_string($queryToken) && $queryToken !== '') {
            return $this->enterByToken($request, $queryToken);
        }

        $guestId = $request->session()->get(self::SESSION_WEDDING_GUEST_ID);
        $guest = $guestId ? Guest::query()->find($guestId) : null;

        return view('wedding', [
            'guest' => $guest,
            'event' => config('wedding.event'),
            'faqs' => $this->normalizedFaqs(),
            'songRecommendationsEnabled' => (bool) config('wedding.song_recommendations.enabled', true),
            'ownSongRecommendations' => PublicSongRecommendationController::ownSuggestions($request),
            'publicSongRecommendations' => PublicSongRecommendationController::publicFeed(),
        ]);
    }

    /**
     * Dedicated page that groups "How to get there" (maps) and the RSVP form,
     * linked from the navbar so guests have a focused place to reply.
     */
    public function attend(Request $request): View
    {
        $guestId = $request->session()->get(self::SESSION_WEDDING_GUEST_ID);
        $guest = $guestId ? Guest::query()->find($guestId) : null;

        return view('wedding-attend', [
            'guest' => $guest,
            'event' => config('wedding.event'),
            'rsvpDeadline' => $this->resolveRsvpDeadline(),
            'rsvpConfirmation' => $request->session()->get('wedding_rsvp_summary'),
        ]);
    }

    /**
     * Returns a normalised view of the configured RSVP deadline:
     *   - `date`: Carbon end-of-day in the event timezone (or null if unconfigured),
     *   - `formatted`: locale-formatted full date string,
     *   - `passed`: true when the deadline is in the past.
     *
     * @return array{date: \Illuminate\Support\Carbon|null, formatted: ?string, passed: bool}
     */
    private function resolveRsvpDeadline(): array
    {
        $raw = config('wedding.rsvp.deadline');
        if (! is_string($raw) || trim($raw) === '') {
            return ['date' => null, 'formatted' => null, 'passed' => false];
        }

        $event = config('wedding.event');
        $timezone = is_array($event) && isset($event['timezone']) && is_string($event['timezone']) && $event['timezone'] !== ''
            ? $event['timezone']
            : (string) config('app.timezone', 'UTC');

        try {
            $deadline = \Illuminate\Support\Carbon::parse(trim($raw), $timezone)->endOfDay();
        } catch (\Throwable) {
            return ['date' => null, 'formatted' => null, 'passed' => false];
        }

        $locale = app()->getLocale();
        $formatted = $deadline->copy()->locale($locale)->isoFormat('LL');

        return [
            'date' => $deadline,
            'formatted' => $formatted,
            'passed' => $deadline->isPast(),
        ];
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    private function normalizedFaqs(): array
    {
        $raw = config('wedding.faqs', []);
        if (! is_array($raw)) {
            return [];
        }

        $clean = [];
        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $q = isset($item['question']) && is_string($item['question']) ? trim($item['question']) : '';
            $a = isset($item['answer']) && is_string($item['answer']) ? trim($item['answer']) : '';
            if ($q === '' || $a === '') {
                continue;
            }
            $clean[] = ['question' => $q, 'answer' => $a];
        }

        return $clean;
    }

    public function enterByToken(Request $request, string $token): RedirectResponse
    {
        $guest = Guest::query()->where('token', $token)->first();

        if (! $guest) {
            return redirect()
                ->route('wedding.show')
                ->with('wedding_error', __('This invitation link is not valid or is no longer active.'));
        }

        $request->session()->put(self::SESSION_WEDDING_GUEST_ID, $guest->id);

        return redirect()->route('wedding.show');
    }

    public function storeRsvp(StoreRsvpRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $guestId = $request->session()->get(self::SESSION_WEDDING_GUEST_ID);
        $guest = $guestId ? Guest::query()->find($guestId) : null;

        $attending = $validated['rsvp_status'] === 'yes';
        $companionNames = $attending ? ($validated['companion_names'] ?? []) : [];

        if (! $guest) {
            $guest = Guest::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'rsvp_status' => $validated['rsvp_status'],
                'guests_count' => $attending ? (int) $validated['guests_count'] : null,
                'companion_names' => $companionNames !== [] ? $companionNames : null,
                'notes' => $validated['notes'] ?? null,
            ]);
            $request->session()->put(self::SESSION_WEDDING_GUEST_ID, $guest->id);
            $hadPriorRsvp = false;
        } else {
            /** US-07: editing — any prior submitted RSVP (status was already chosen). */
            $hadPriorRsvp = $guest->rsvp_status !== null;

            $update = [
                'rsvp_status' => $validated['rsvp_status'],
                'guests_count' => $attending ? (int) $validated['guests_count'] : null,
                'companion_names' => $companionNames !== [] ? $companionNames : null,
            ];
            if (array_key_exists('notes', $validated)) {
                $update['notes'] = $validated['notes'];
            }
            $guest->update($update);
        }

        $guest->refresh();

        $confirmationEmailSent = false;
        if (config('wedding.rsvp.send_confirmation_email') && filled($guest->email)) {
            try {
                Mail::to($guest->email)->send(new RsvpConfirmationMail($guest));
                $confirmationEmailSent = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->sendAdminRsvpNotification($guest, $hadPriorRsvp);

        return redirect()
            ->route('wedding.attend')
            ->with(
                'wedding_success',
                $hadPriorRsvp
                    ? __('Your RSVP has been updated.')
                    : __('Thank you — your response has been saved.'),
            )
            ->with('wedding_confirmation_email_sent', $confirmationEmailSent)
            ->with('wedding_rsvp_summary', $this->buildRsvpSummary($guest, $hadPriorRsvp));
    }

    /**
     * Snapshot of the just-saved RSVP, rendered on the Attend page as a
     * dedicated thank-you card (US-05/US-07 UX).
     *
     * @return array{
     *   attending: bool,
     *   name: string,
     *   email: ?string,
     *   guests_count: ?int,
     *   companion_names: array<int, string>,
     *   is_update: bool,
     * }
     */
    private function buildRsvpSummary(Guest $guest, bool $hadPriorRsvp): array
    {
        $companions = is_array($guest->companion_names) ? $guest->companion_names : [];
        $companions = array_values(array_filter(array_map(
            static fn ($n): string => is_string($n) ? trim($n) : '',
            $companions,
        ), static fn (string $n): bool => $n !== ''));

        return [
            'attending' => $guest->rsvp_status === 'yes',
            'name' => (string) $guest->name,
            'email' => $guest->email,
            'guests_count' => $guest->guests_count !== null ? (int) $guest->guests_count : null,
            'companion_names' => $companions,
            'is_update' => $hadPriorRsvp,
        ];
    }

    /** US-24: optional email to the organiser when an RSVP is saved. */
    private function sendAdminRsvpNotification(Guest $guest, bool $hadPriorRsvp): void
    {
        $to = config('wedding.rsvp.notify_admin_email');
        if (! is_string($to) || $to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($to)->send(new RsvpAdminNotificationMail($guest, $hadPriorRsvp));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
