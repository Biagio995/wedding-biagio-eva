<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistryItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'product_url',
        'sort_order',
        'is_active',
        'claimed_by_guest_id',
        'claimed_at',
        'claimed_by_name',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'claimed_at' => 'datetime',
        ];
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'claimed_by_guest_id');
    }

    /** True if reserved (by an invited guest or anonymously). */
    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }

    /** Anonymous reservation: timestamp set, no guest row. */
    public function isAnonymousClaim(): bool
    {
        return $this->claimed_at !== null && $this->claimed_by_guest_id === null;
    }

    public function isClaimedByGuest(Guest $guest): bool
    {
        return $this->claimed_by_guest_id !== null
            && (int) $this->claimed_by_guest_id === (int) $guest->id;
    }
}
