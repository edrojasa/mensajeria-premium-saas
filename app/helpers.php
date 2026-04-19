<?php

use App\Support\TenantManager;

if (! function_exists('tenant_id')) {
    /**
     * ID de la organización activa en sesión (contexto multi-tenant).
     */
    function tenant_id(): ?int
    {
        if (! app()->bound(TenantManager::class)) {
            return null;
        }

        return app(TenantManager::class)->currentOrganizationId();
    }
}
