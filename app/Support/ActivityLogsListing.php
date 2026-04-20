<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class ActivityLogsListing
{
    /**
     * Misma consulta que {@see \App\Http\Controllers\ActivityLogController::index} (filtros GET + tenant).
     */
    public static function filteredQuery(Request $request): Builder
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        return ActivityLog::query()
            ->where('organization_id', $orgId)
            ->with('user')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', (int) $request->query('user_id')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->query('action')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('date_to')))
            ->latest('created_at');
    }
}
