<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'locale',
        'token',
        'rsvp_status',
        'guests_count',
        'companion_names',
        'seating_table_id',
        'notes',
        'rsvp_reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'guests_count' => 'integer',
            'companion_names' => 'array',
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

    /** Locale for outbound emails to this guest (independent of site UI language). */
    public function mailLocale(): string
    {
        $allowed = array_keys(config('wedding.mail_locales', []));
        if (is_string($this->locale) && in_array($this->locale, $allowed, true)) {
            return $this->locale;
        }

        return (string) config('wedding.mail.default_locale', 'en');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function seatingTable(): BelongsTo
    {
        return $this->belongsTo(SeatingTable::class, 'seating_table_id');
    }

    public function claimedRegistryItems(): HasMany
    {
        return $this->hasMany(RegistryItem::class, 'claimed_by_guest_id');
    }

    public function songRecommendations(): HasMany
    {
        return $this->hasMany(SongRecommendation::class);
    }
}
