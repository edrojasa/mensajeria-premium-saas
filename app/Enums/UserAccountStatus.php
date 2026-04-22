<?php

namespace App\Enums;

/**
 * Estado global de cuenta (login). Compatible PHP 8.0 (sin enum nativo).
 */
final class UserAccountStatus
{
    public const ACTIVE = 'activo';

    public const SUSPENDED = 'suspendido';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [self::ACTIVE, self::SUSPENDED];
    }

    public static function isSuspended(?string $value): bool
    {
        return $value === self::SUSPENDED;
    }

    public static function label(?string $value): string
    {
        return match ($value) {
            self::SUSPENDED => __('users.account_badge_suspended'),
            default => __('users.account_badge_active'),
        };
    }
}
