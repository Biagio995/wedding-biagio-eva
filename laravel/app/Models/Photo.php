<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    protected $fillable = [
        'guest_id',
        'file_path',
        'original_filename',
        'approved',
    ];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /** US-13: when `gallery.public_feed.only_approved`, hide pending moderation. */
    public function scopeForPublicFeed(Builder $query): void
    {
        if (config('gallery.public_feed.only_approved', false)) {
            $query->where('approved', true);
        }
    }

    /** US-14: filter public lists by calendar day of `created_at` (upload time). */
    public function scopeWhereUploadedOnDate(Builder $query, ?string $dateYmd): void
    {
        if ($dateYmd === null || $dateYmd === '') {
            return;
        }

        $query->whereDate('created_at', $dateYmd);
    }
}
