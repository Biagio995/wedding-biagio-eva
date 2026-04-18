<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /** Paginated audit log with optional `action` filter (startswith match). */
    public function index(Request $request): View
    {
        $action = $request->query('action');
        if (! is_string($action) || $action === '') {
            $action = null;
        }

        $query = AuditLog::query()->orderByDesc('id');
        if ($action !== null) {
            // Escape LIKE metachars before appending wildcard.
            $escaped = addcslashes($action, '\\%_');
            $query->where('action', 'like', $escaped.'%');
        }

        $logs = $query->simplePaginate(50)->withQueryString();

        $availableActions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->all();

        return view('admin.audit.index', [
            'logs' => $logs,
            'action' => $action,
            'availableActions' => $availableActions,
        ]);
    }
}
