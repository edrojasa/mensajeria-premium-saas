<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->canViewAuditLogs(), 403);

        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $actions = ActivityLog::query()
            ->where('organization_id', $orgId)
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $usersForFilter = User::query()
            ->whereHas('organizations', fn ($q) => $q->where('organizations.id', $orgId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $logs = ActivityLog::query()
            ->where('organization_id', $orgId)
            ->with('user')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', (int) $request->query('user_id')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->query('action')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('date_to')))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('logs.index', [
            'logs' => $logs,
            'actions' => $actions,
            'usersForFilter' => $usersForFilter,
        ]);
    }
}
