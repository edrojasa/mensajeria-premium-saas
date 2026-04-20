<?php

namespace App\Finance;

final class PaymentStatus
{
    public const PENDING = 'pending';

    public const PAID = 'paid';

    /** @return list<string> */
    public static function all(): array
    {
        return [self::PENDING, self::PAID];
    }

    public static function label(string $status): string
    {
        return __('finance.payment_statuses.'.$status);
    }
}
