<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminSeatingTableRequest;
use App\Http\Requests\UpdateAdminSeatingTableRequest;
use App\Models\Guest;
use App\Models\SeatingTable;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SeatingTableController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    /** Seating chart overview: tables with assigned guests + unassigned list. */
    public function index(): View
    {
        $tables = SeatingTable::query()
            ->with(['guests' => fn ($q) => $q->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $unassigned = Guest::query()
            ->whereNull('seating_table_id')
            ->where(function ($q): void {
                $q->where('rsvp_status', 'yes')->orWhereNull('rsvp_status');
            })
            ->orderBy('name')
            ->get();

        return view('admin.seating.index', [
            'tables' => $tables,
            'unassigned' => $unassigned,
        ]);
    }

    public function store(StoreAdminSeatingTableRequest $request): RedirectResponse
    {
        $table = SeatingTable::query()->create($request->validated());
        $this->audit->log('seating.table.created', $table, ['label' => $table->label]);

        return redirect()
            ->route('admin.seating.index')
            ->with('status', __('Table created.'));
    }

    public function edit(SeatingTable $seatingTable): View
    {
        return view('admin.seating.edit', [
            'seatingTable' => $seatingTable,
        ]);
    }

    public function update(UpdateAdminSeatingTableRequest $request, SeatingTable $seatingTable): RedirectResponse
    {
        $seatingTable->update($request->validated());
        $this->audit->log('seating.table.updated', $seatingTable, ['label' => $seatingTable->label]);

        return redirect()
            ->route('admin.seating.index')
            ->with('status', __('Table updated.'));
    }

    public function destroy(SeatingTable $seatingTable): RedirectResponse
    {
        $this->audit->log('seating.table.deleted', $seatingTable, ['label' => $seatingTable->label]);
        // FK uses nullOnDelete → guests at this table become unassigned automatically.
        $seatingTable->delete();

        return redirect()
            ->route('admin.seating.index')
            ->with('status', __('Table deleted.'));
    }

    /** Assign a guest to this table (or remove when `seating_table_id` is empty). */
    public function assign(Request $request, SeatingTable $seatingTable): RedirectResponse
    {
        $data = $request->validate([
            'guest_id' => ['required', 'integer', Rule::exists('guests', 'id')],
        ]);

        Guest::query()
            ->whereKey($data['guest_id'])
            ->update(['seating_table_id' => $seatingTable->id]);

        $this->audit->log('seating.assign', $seatingTable, [
            'guest_id' => (int) $data['guest_id'],
            'table' => $seatingTable->label,
        ]);

        return redirect()
            ->route('admin.seating.index')
            ->with('status', __('Guest assigned to table.'));
    }

    public function unassign(Guest $guest): RedirectResponse
    {
        $previous = $guest->seating_table_id;
        $guest->update(['seating_table_id' => null]);
        $this->audit->log('seating.unassign', $guest, ['previous_table_id' => $previous]);

        return redirect()
            ->route('admin.seating.index')
            ->with('status', __('Guest removed from table.'));
    }
}
