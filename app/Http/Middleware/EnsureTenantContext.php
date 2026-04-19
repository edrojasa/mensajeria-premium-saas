<?php

namespace App\Http\Middleware;

use App\Support\TenantManager;
use Closure;
use Illuminate\Http\Request;

class EnsureTenantContext
{
    public function __construct(
        private TenantManager $tenantManager
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        $this->tenantManager->synchronizeSessionOrganization($user);

        if (! $user->organizations()->exists()) {
            abort(403, __('Tu usuario no está asociado a ninguna organización.'));
        }

        if ($this->tenantManager->currentOrganizationId() === null) {
            abort(500, __('No se pudo establecer el contexto de organización.'));
        }

        return $next($request);
    }
}
