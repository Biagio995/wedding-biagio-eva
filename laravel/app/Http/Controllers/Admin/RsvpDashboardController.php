<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\View\View;

class RsvpDashboardController extends Controller
{
    /** US-19: RSVP summary for monitoring attendance. */
    public function index(): View
    {
        $stats = [
            'total_guests' => Guest::query()->count(),
            'pending' => Guest::query()->whereNull('rsvp_status')->count(),
            'yes' => Guest::query()->where('rsvp_status', 'yes')->count(),
            'no' => Guest::query()->where('rsvp_status', 'no')->count(),
            'attending_people' => (int) Guest::query()->where('rsvp_status', 'yes')->sum('guests_count'),
        ];

        $stats['responded'] = $stats['yes'] + $stats['no'];

        return view('admin.rsvp.dashboard', [
            'stats' => $stats,
        ]);
    }
}
