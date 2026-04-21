<?php

namespace App\Exports;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountsReceivableExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Builder $query
    ) {
    }

    public function headings(): array
    {
        return ['cliente', 'envio', 'valor', 'estado', 'fecha'];
    }

    public function collection(): Collection
    {
        /** @var Collection<int, Shipment> $rows */
        $rows = $this->query->get();

        return $rows->map(function (Shipment $shipment): array {
            return [
                'cliente' => $shipment->customer?->name ?? __('finance.unknown_customer_group'),
                'envio' => $shipment->tracking_number,
                'valor' => $shipment->balanceDue(),
                'estado' => $shipment->payment_status,
                'fecha' => optional($shipment->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            ];
        });
    }
}
