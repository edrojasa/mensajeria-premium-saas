<?php

namespace App\Support;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

final class CustomerCodeGenerator
{
    public function generate(int $organizationId): string
    {
        return DB::transaction(function () use ($organizationId) {
            $query = Customer::withoutGlobalScopes()
                ->where('organization_id', $organizationId)
                ->where('customer_code', 'like', 'CL-%')
                ->orderByDesc('customer_code');

            if (DB::connection()->getDriverName() !== 'sqlite') {
                $query->lockForUpdate();
            }

            $max = $query->value('customer_code');

            $n = 1;
            if ($max !== null && preg_match('/CL-(\d+)/', (string) $max, $m)) {
                $n = (int) $m[1] + 1;
            }

            return 'CL-'.str_pad((string) $n, 6, '0', STR_PAD_LEFT);
        });
    }
}
