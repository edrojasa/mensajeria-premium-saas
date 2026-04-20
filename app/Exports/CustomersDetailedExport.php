<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersDetailedExport implements FromCollection, WithHeadings
{
    /**
     * @param  Collection<int, array<int, string|null>>  $rows
     */
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            __('exports.customers_col_name'),
            __('exports.customers_col_customer_code'),
            __('exports.customers_col_phone'),
            __('exports.customers_col_email'),
            __('exports.customers_col_document'),
            __('exports.customers_col_tracking'),
            __('exports.customers_col_shipment_status'),
            __('exports.customers_col_shipment_date'),
            __('exports.customers_col_messenger'),
            __('exports.customers_col_cost'),
            __('exports.customers_col_payment_type'),
            __('exports.customers_col_payment_status'),
        ];
    }
}
