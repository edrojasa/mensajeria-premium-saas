<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Support\ActivityLogsListing;
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

        $logs = ActivityLogsListing::filteredQuery($request)
            ->paginate(30)
            ->withQueryString();

        return view('logs.index', [
            'logs' => $logs,
            'actions' => $actions,
            'usersForFilter' => $usersForFilter,
        ]);
    }
}
