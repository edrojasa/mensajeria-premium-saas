<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query->clone();
    }

    public function headings(): array
    {
        return [
            __('exports.customers_col_name'),
            __('exports.customers_col_phone'),
            __('exports.customers_col_email'),
            __('exports.customers_col_document'),
        ];
    }

    /**
     * @param  \App\Models\Customer  $customer
     * @return array<int, string|null>
     */
    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->phone,
            $customer->email,
            $customer->document,
        ];
    }
}
