<?php

namespace App\Exports;

use App\Finance\PaymentStatus;
use App\Finance\PaymentType;
use App\Shipments\ShipmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShipmentsExport implements FromQuery, WithHeadings, WithMapping
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
            __('exports.shipments_col_tracking'),
            __('exports.shipments_col_customer_code'),
            __('exports.shipments_col_customer'),
            __('exports.shipments_col_recipient'),
            __('exports.shipments_col_dest_address'),
            __('exports.shipments_col_dest_city'),
            __('exports.shipments_col_origin_city'),
            __('exports.shipments_col_status'),
            __('exports.shipments_col_messenger'),
            __('exports.shipments_col_created'),
            __('exports.shipments_col_delivered'),
            __('exports.shipments_col_cost'),
            __('exports.shipments_col_payment_type'),
            __('exports.shipments_col_payment_status'),
        ];
    }

    /**
     * @param  \App\Models\Shipment  $shipment
     * @return array<int, string|int|float|null>
     */
    public function map($shipment): array
    {
        $deliveredAt = $shipment->delivered_logged_at ?? null;

        return [
            $shipment->tracking_number,
            $shipment->customer?->customer_code ?? '—',
            $shipment->customer?->name ?? '—',
            $shipment->recipient_name,
            $shipment->destination_address_line,
            $shipment->destination_city,
            $shipment->origin_city,
            ShipmentStatus::label($shipment->status),
            $shipment->assignedCourier?->name ?? __('shipments.unassigned_courier'),
            $shipment->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '',
            $deliveredAt
                ? \Carbon\Carbon::parse($deliveredAt)->timezone(config('app.timezone'))->format('d/m/Y H:i')
                : '—',
            $shipment->cost !== null ? number_format((float) $shipment->cost, 2, ',', '.') : '—',
            $shipment->payment_type ? PaymentType::label($shipment->payment_type) : '—',
            $shipment->payment_status ? PaymentStatus::label($shipment->payment_status) : '—',
        ];
    }
}
