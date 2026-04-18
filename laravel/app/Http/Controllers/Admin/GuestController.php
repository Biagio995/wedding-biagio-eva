<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminGuestCsvImportRequest;
use App\Http\Requests\StoreAdminGuestRequest;
use App\Http\Requests\UpdateAdminGuestRequest;
use App\Models\Guest;
use App\Services\AuditLogger;
use App\Services\GuestCsvImporter;
use App\Services\WeddingInviteQrGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

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
        $this->audit->log('guest.csv.imported', null, [
            'created' => $result->created,
            'errors' => count($result->errors),
        ]);

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
        $this->audit->log('guest.created', $guest, ['name' => $guest->name]);

        return redirect()
            ->route('admin.guests.create')
            ->with('status', __('Guest created.'))
            ->with('created_guest', [
                'id' => $guest->id,
                'name' => $guest->name,
                'invite_url' => route('wedding.enter', ['token' => $guest->token]),
            ]);
    }

    public function edit(Request $request, Guest $guest): View
    {
        $filter = $request->query('rsvp', 'all');
        if (! is_string($filter) || ! in_array($filter, ['all', 'yes', 'no', 'pending'], true)) {
            $filter = 'all';
        }

        return view('admin.guests.edit', [
            'guest' => $guest,
            'filter' => $filter,
        ]);
    }

    public function update(UpdateAdminGuestRequest $request, Guest $guest): RedirectResponse
    {
        $data = $request->validated();
        if (($data['rsvp_status'] ?? null) === null) {
            $data['rsvp_reminder_sent_at'] = null;
        }

        $before = [
            'name' => $guest->name,
            'email' => $guest->email,
            'rsvp_status' => $guest->rsvp_status,
            'guests_count' => $guest->guests_count,
        ];
        $guest->update($data);
        $after = $guest->only(array_keys($before));
        $changed = array_keys(array_diff_assoc($after, $before));
        $this->audit->log('guest.updated', $guest, ['changed' => $changed]);

        $filter = $request->input('return_rsvp', 'all');
        if (! is_string($filter) || ! in_array($filter, ['all', 'yes', 'no', 'pending'], true)) {
            $filter = 'all';
        }

        return redirect()
            ->route('admin.guests.index', ['rsvp' => $filter])
            ->with('status', __('Guest updated.'));
    }

    public function destroy(Request $request, Guest $guest): RedirectResponse
    {
        $this->audit->log('guest.deleted', $guest, ['name' => $guest->name]);
        $guest->delete();

        $filter = $request->query('rsvp', 'all');
        if (! is_string($filter) || ! in_array($filter, ['all', 'yes', 'no', 'pending'], true)) {
            $filter = 'all';
        }

        return redirect()
            ->route('admin.guests.index', ['rsvp' => $filter])
            ->with('status', __('Guest deleted.'));
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

    /**
     * Streams the full guest/RSVP list as a UTF-8 CSV (with BOM so Excel opens it correctly).
     */
    public function export(Request $request): StreamedResponse
    {
        $filter = $request->query('rsvp', 'all');
        if (! is_string($filter) || ! in_array($filter, ['all', 'yes', 'no', 'pending'], true)) {
            $filter = 'all';
        }

        $filename = 'rsvp-export-'.now()->format('Y-m-d-Hi').'.csv';

        $this->audit->log('guest.csv.exported', null, ['filter' => $filter]);

        return response()->streamDownload(function () use ($filter): void {
            $handle = fopen('php://output', 'wb');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM so Excel detects encoding correctly.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'id',
                'name',
                'email',
                'token',
                'rsvp_status',
                'guests_count',
                'companion_names',
                'notes',
                'rsvp_reminder_sent_at',
                'created_at',
                'updated_at',
            ], ',', '"', '\\');

            $query = Guest::query();
            match ($filter) {
                'yes' => $query->where('rsvp_status', 'yes'),
                'no' => $query->where('rsvp_status', 'no'),
                'pending' => $query->whereNull('rsvp_status'),
                default => null,
            };

            $query->orderBy('name')->chunk(200, function ($guests) use ($handle): void {
                foreach ($guests as $guest) {
                    $companions = is_array($guest->companion_names) ? $guest->companion_names : [];
                    $companionsCsv = implode(' | ', array_filter(array_map(
                        static fn ($n): string => is_string($n) ? trim($n) : '',
                        $companions,
                    ), static fn (string $n): bool => $n !== ''));

                    fputcsv($handle, [
                        $guest->id,
                        (string) $guest->name,
                        (string) ($guest->email ?? ''),
                        (string) $guest->token,
                        (string) ($guest->rsvp_status ?? ''),
                        $guest->rsvp_status === 'yes' ? (string) ($guest->guests_count ?? '') : '',
                        $companionsCsv,
                        (string) ($guest->notes ?? ''),
                        $guest->rsvp_reminder_sent_at?->toIso8601String() ?? '',
                        $guest->created_at?->toIso8601String() ?? '',
                        $guest->updated_at?->toIso8601String() ?? '',
                    ], ',', '"', '\\');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
