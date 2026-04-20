<?php

use App\Support\TenantManager;

if (! function_exists('brand_logo_relative_paths')) {
    /**
     * Preferencia: nuevo branding primero, luego legado.
     *
     * @return list<string>
     */
    function brand_logo_relative_paths(): array
    {
        return [
            'images/logo2png.png',
            'images/Logo Mensajeria.png',
        ];
    }
}

if (! function_exists('brand_logo_public_path')) {
    /**
     * Ruta absoluta al logo oficial si existe (public/images/logo2png.png por defecto).
     */
    function brand_logo_public_path(): ?string
    {
        foreach (brand_logo_relative_paths() as $relative) {
            $path = public_path($relative);
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}

if (! function_exists('brand_logo_asset')) {
    /**
     * URL pública del logo o null si no está el archivo.
     */
    function brand_logo_asset(): ?string
    {
        foreach (brand_logo_relative_paths() as $relative) {
            if (is_file(public_path($relative))) {
                return asset($relative);
            }
        }

        return null;
    }
}

if (! function_exists('brand_logo_can_embed_in_pdf')) {
    /**
     * Dompdf exige GD para rasterizar PNG en PDF; sin GD se usa solo texto de marca.
     */
    function brand_logo_can_embed_in_pdf(): bool
    {
        return brand_logo_public_path() !== null && extension_loaded('gd');
    }
}

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
