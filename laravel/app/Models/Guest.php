<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'token',
        'rsvp_status',
        'guests_count',
        'notes',
        'rsvp_reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'guests_count' => 'integer',
            'rsvp_reminder_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Guest $guest): void {
            if ($guest->token === null || $guest->token === '') {
                $guest->token = static::generateUniqueToken();
            }
        });
    }

    /**
     * Cryptographically random token, unique in DB (US-03).
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(48);
        } while (static::query()->where('token', $token)->exists());

        return $token;
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function claimedRegistryItems(): HasMany
    {
        return $this->hasMany(RegistryItem::class, 'claimed_by_guest_id');
    }
}
