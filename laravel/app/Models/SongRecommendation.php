<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A song suggestion from a guest for the DJ to consider.
 *
 * Submissions either belong to a logged-in guest (linked via `guest_id`) or are
 * anonymous (just a `submitted_by` label and a session-scoped `session_token`
 * that lets the same browser see and remove its own entries).
 */
class SongRecommendation extends Model
{
    protected $fillable = [
        'guest_id',
        'submitted_by',
        'title',
        'artist',
        'notes',
        'session_token',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Human-friendly label for admin/public lists, preferring the linked guest.
     */
    public function displayAuthor(): string
    {
        $guestName = $this->guest?->name;
        if (is_string($guestName) && $guestName !== '') {
            return $guestName;
        }

        $fallback = (string) ($this->submitted_by ?? '');

        return $fallback !== '' ? $fallback : '—';
    }
}
