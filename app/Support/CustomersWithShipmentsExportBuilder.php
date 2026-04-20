<?php

namespace App\Support;

use App\Finance\PaymentStatus;
use App\Finance\PaymentType;
use App\Models\Customer;
use App\Shipments\ShipmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class CustomersWithShipmentsExportBuilder
{
    /**
     * Una fila por envío; si el cliente no tiene envíos, una fila con datos del cliente y envío vacío.
     *
     * @return Collection<int, array<int, string|null>>
     */
    public static function rowsForListingQuery(Builder $customerQuery): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Customer> $customers */
        $customers = $customerQuery->clone()
            ->with([
                'shipments' => fn ($q) => $q->with('assignedCourier')->latest(),
            ])
            ->get();

        $out = collect();

        foreach ($customers as $customer) {
            if ($customer->shipments->isEmpty()) {
                $out->push([
                    $customer->name,
                    $customer->customer_code,
                    $customer->phone,
                    $customer->email,
                    $customer->document,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                ]);

                continue;
            }

            foreach ($customer->shipments as $shipment) {
                $out->push([
                    $customer->name,
                    $customer->customer_code,
                    $customer->phone,
                    $customer->email,
                    $customer->document,
                    $shipment->tracking_number,
                    ShipmentStatus::label($shipment->status),
                    $shipment->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i'),
                    $shipment->assignedCourier?->name ?? __('shipments.unassigned_courier'),
                    $shipment->cost !== null ? number_format((float) $shipment->cost, 2, ',', '.') : null,
                    $shipment->payment_type ? PaymentType::label($shipment->payment_type) : '—',
                    $shipment->payment_status ? PaymentStatus::label($shipment->payment_status) : '—',
                ]);
            }
        }

        return $out;
    }
}
