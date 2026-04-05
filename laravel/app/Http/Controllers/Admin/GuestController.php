<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminGuestCsvImportRequest;
use App\Http\Requests\StoreAdminGuestRequest;
use App\Models\Guest;
use App\Services\GuestCsvImporter;
use App\Services\WeddingInviteQrGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GuestController extends Controller
{
    /** US-20: guest list with RSVP status (optional filter: rsvp=all|yes|no|pending). */
    public function index(Request $request): View
    {
        $filter = $request->query('rsvp', 'all');
        if (! is_string($filter) || ! in_array($filter, ['all', 'yes', 'no', 'pending'], true)) {
            $filter = 'all';
        }

        $query = Guest::query();

        match ($filter) {
            'yes' => $query->where('rsvp_status', 'yes'),
            'no' => $query->where('rsvp_status', 'no'),
            'pending' => $query->whereNull('rsvp_status'),
            default => null,
        };

        $guests = $query
            ->orderBy('name')
            ->simplePaginate(30)
            ->withQueryString();

        return view('admin.guests.index', [
            'guests' => $guests,
            'filter' => $filter,
        ]);
    }

    public function create(): View
    {
        return view('admin.guests.create');
    }

    public function importForm(): View
    {
        return view('admin.guests.import');
    }

    /** US-18: bulk-create guests from a CSV file (see import view for format). */
    public function importStore(StoreAdminGuestCsvImportRequest $request, GuestCsvImporter $importer): RedirectResponse
    {
        $uploaded = $request->file('file');
        if ($uploaded === null) {
            return redirect()
                ->route('admin.guests.import')
                ->withErrors(['file' => __('Choose a file.')]);
        }

        $result = $importer->import($uploaded->getRealPath());

        return redirect()
            ->route('admin.guests.import')
            ->with('import_result', [
                'created' => $result->created,
                'errors' => $result->errors,
            ]);
    }

    public function store(StoreAdminGuestRequest $request): RedirectResponse
    {
        $guest = Guest::query()->create($request->validated());

        return redirect()
            ->route('admin.guests.create')
            ->with('status', __('Guest created.'))
            ->with('created_guest', [
                'id' => $guest->id,
                'name' => $guest->name,
                'invite_url' => route('wedding.enter', ['token' => $guest->token]),
            ]);
    }

    /** US-17: PNG QR encoding the guest’s absolute wedding invitation URL. */
    public function qr(Request $request, Guest $guest, WeddingInviteQrGenerator $generator): Response
    {
        $inviteUrl = route('wedding.enter', ['token' => $guest->token], absolute: true);
        $result = $generator->make($inviteUrl);

        $headers = [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => 'private, max-age=3600',
        ];

        if ($request->boolean('download')) {
            $headers['Content-Disposition'] = 'attachment; filename="'.$this->qrDownloadFilename($guest).'"';
        }

        return response($result->getString(), 200, $headers);
    }

    private function qrDownloadFilename(Guest $guest): string
    {
        $base = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $guest->name);
        $base = trim(is_string($base) ? $base : '', '-');
        if ($base === '') {
            $base = 'guest';
        }

        return 'invite-'.$guest->id.'-'.$base.'.png';
    }
}
