<?php

namespace App\Organizations;

final class OrganizationRole
{
    /** Propietario / configuración total (synonym legacy: owner migrado en BD a admin) */
    public const ADMIN = 'admin';

    /** Backoffice operativo */
    public const OPERADOR = 'operador';

    /** Solo envíos asignados */
    public const MENSAJERO = 'mensajero';

    /** @var list<string> */
    public const ALL = [
        self::ADMIN,
        self::OPERADOR,
        self::MENSAJERO,
    ];

    /** @var list<string> */
    public const ASSIGNABLE_FOR_SHIPMENTS = [
        self::ADMIN,
        self::OPERADOR,
    ];

    /**
     * Roles equivalentes a administrador (legacy DB puede conservar valores antiguos tras migraciones).
     */
    public static function hasFullAccess(?string $role): bool
    {
        return in_array((string) $role, [
            self::ADMIN,
            'owner',
        ], true);
    }

    /**
     * Puede crear/enviar y gestionar clientes (admin u operador activo).
     */
    public static function canOperateLogistics(?string $role): bool
    {
        return in_array((string) $role, [
            self::ADMIN,
            self::OPERADOR,
            'owner',
            'member',
        ], true);
    }

    public static function label(string $role): string
    {
        return __("users.role_labels.{$role}");
    }
}
