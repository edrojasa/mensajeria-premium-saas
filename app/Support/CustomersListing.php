<?php

namespace App\Support;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class CustomersListing
{
    public static function filteredQuery(Request $request): Builder
    {
        $q = trim((string) $request->query('q', ''));

        return Customer::query()
            ->withCount('shipments')
            ->when(! $request->boolean('inactive'), function (Builder $qb): void {
                $qb->where('customers.is_active', true);
            })
            ->when($q !== '', function (Builder $qb) use ($q): void {
                $like = '%'.$q.'%';
                $qb->where(function ($inner) use ($like): void {
                    $inner->where('name', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('customer_code', 'like', $like);
                });
            })
            ->orderBy('name');
    }
}
