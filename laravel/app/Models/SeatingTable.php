<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatingTable extends Model
{
    protected $fillable = [
        'label',
        'capacity',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    /** Total of main guests + their listed companions sitting at this table (US: seating capacity). */
    public function occupiedSeats(): int
    {
        $total = 0;
        foreach ($this->guests as $guest) {
            if ($guest->rsvp_status === 'no') {
                continue;
            }
            $count = $guest->rsvp_status === 'yes' && is_int($guest->guests_count) && $guest->guests_count > 0
                ? $guest->guests_count
                : 1;
            $total += $count;
        }

        return $total;
    }
}
