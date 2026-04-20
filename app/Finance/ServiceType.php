<?php

namespace App\Finance;

final class ServiceType
{
    public const STANDARD = 'standard';

    public const EXPRESS = 'express';

    public const ECONOMY = 'economy';

    /** @return list<string> */
    public static function all(): array
    {
        return [self::STANDARD, self::EXPRESS, self::ECONOMY];
    }

    public static function label(string $type): string
    {
        return __('finance.service_types.'.$type);
    }
}
