<?php

namespace App\Finance;

final class PaymentType
{
    public const CASH = 'cash';

    public const CREDIT = 'credit';

    /** @return list<string> */
    public static function all(): array
    {
        return [self::CASH, self::CREDIT];
    }

    public static function label(string $type): string
    {
        return __('finance.payment_types.'.$type);
    }
}
