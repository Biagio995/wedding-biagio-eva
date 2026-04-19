<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\RegistryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistryController extends Controller
{
    /** Session key: list of registry item IDs reserved anonymously on this browser. */
    public const SESSION_ANONYMOUS_CLAIM_IDS = 'registry_anonymous_claim_ids';

    public function show(Request $request): View
    {
        $guestId = $request->session()->get(WeddingController::SESSION_WEDDING_GUEST_ID);
        $guest = $guestId ? Guest::query()->find($guestId) : null;

        $allActiveItems = RegistryItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->pruneAnonymousClaimSession($request, $allActiveItems);

        $availableItems = $allActiveItems
            ->filter(fn (RegistryItem $item) => ! $item->isClaimed())
            ->values();

        $hasActiveRegistryItems = $allActiveItems->isNotEmpty();
        $showEmptyCatalog = ! $hasActiveRegistryItems;
        $noGiftsAvailableForYou = $hasActiveRegistryItems && $availableItems->isEmpty();

        return view('registry', [
            'guest' => $guest,
            'availableItems' => $availableItems,
            'showEmptyCatalog' => $showEmptyCatalog,
            'noGiftsAvailableForYou' => $noGiftsAvailableForYou,
            'event' => config('wedding.event'),
        ]);
    }

    public function claim(Request $request, RegistryItem $registryItem): RedirectResponse
    {
        if (! $registryItem->is_active) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'claim_message' => ['nullable', 'string', 'max:1000'],
        ]);
        $reserverName = trim($validated['name']);
        if ($reserverName === '') {
            return redirect()
                ->route('registry.show')
                ->withErrors(['name' => __('Please enter your name.')])
                ->withInput();
        }
        $claimMessage = isset($validated['claim_message']) && is_string($validated['claim_message'])
            ? trim($validated['claim_message'])
            : '';
        $claimMessage = $claimMessage !== '' ? $claimMessage : null;

        $guestId = $request->session()->get(WeddingController::SESSION_WEDDING_GUEST_ID);
        $guest = $guestId ? Guest::query()->find($guestId) : null;

        $anonIds = $this->normalizedAnonymousClaimIds($request);

        $result = DB::transaction(function () use ($registryItem, $guest, $request, $anonIds, $reserverName, $claimMessage) {
            /** @var RegistryItem|null $locked */
            $locked = RegistryItem::query()
                ->whereKey($registryItem->id)
                ->lockForUpdate()
                ->first();

            if (! $locked || ! $locked->is_active) {
                abort(404);
            }

            if ($guest) {
                return $this->claimAsGuest($locked, $guest, $reserverName, $claimMessage);
            }

            return $this->claimAsAnonymous($locked, $request, $anonIds, $reserverName, $claimMessage);
        });

        if ($result === 'taken') {
            return redirect()
                ->route('registry.show')
                ->with('registry_error', __('This gift is already reserved by another guest.'));
        }

        return redirect()
            ->route('registry.show')
            ->with('registry_success', __('Your gift selection has been saved.'));
    }

    /**
     * @return 'ok'|'taken'
     */
    private function claimAsGuest(RegistryItem $locked, Guest $guest, string $reserverName, ?string $claimMessage): string
    {
        if ($locked->isClaimedByGuest($guest)) {
            return 'ok';
        }

        if ($locked->isClaimed()) {
            return 'taken';
        }

        $locked->update([
            'claimed_by_guest_id' => $guest->id,
            'claimed_at' => now(),
            'claimed_by_name' => $reserverName,
            'claim_message' => $claimMessage,
        ]);

        return 'ok';
    }

    /**
     * @param  array<int, int>  $anonIds
     * @return 'ok'|'taken'
     */
    private function claimAsAnonymous(RegistryItem $locked, Request $request, array $anonIds, string $reserverName, ?string $claimMessage): string
    {
        if ($locked->claimed_by_guest_id !== null) {
            return 'taken';
        }

        if ($locked->isAnonymousClaim()) {
            return in_array($locked->id, $anonIds, true) ? 'ok' : 'taken';
        }

        $locked->update([
            'claimed_by_guest_id' => null,
            'claimed_at' => now(),
            'claimed_by_name' => $reserverName,
            'claim_message' => $claimMessage,
        ]);

        $anonIds[] = $locked->id;
        $request->session()->put(self::SESSION_ANONYMOUS_CLAIM_IDS, array_values(array_unique($anonIds)));

        return 'ok';
    }

    /**
     * @param  Collection<int, RegistryItem>  $items
     */
    private function pruneAnonymousClaimSession(Request $request, $items): void
    {
        $ids = $this->normalizedAnonymousClaimIds($request);
        if ($ids === []) {
            return;
        }

        $valid = [];
        foreach ($ids as $id) {
            $item = $items->firstWhere('id', $id);
            if ($item instanceof RegistryItem && $item->isAnonymousClaim()) {
                $valid[] = $id;
            }
        }

        if ($valid !== $ids) {
            $request->session()->put(self::SESSION_ANONYMOUS_CLAIM_IDS, $valid);
        }
    }

    /**
     * @return array<int, int>
     */
    private function normalizedAnonymousClaimIds(Request $request): array
    {
        $raw = $request->session()->get(self::SESSION_ANONYMOUS_CLAIM_IDS, []);
        if (! is_array($raw)) {
            return [];
        }

        $ids = [];
        foreach ($raw as $v) {
            if (is_numeric($v)) {
                $ids[] = (int) $v;
            }
        }

        return array_values(array_unique(array_filter($ids, fn (int $id): bool => $id > 0)));
    }
}
