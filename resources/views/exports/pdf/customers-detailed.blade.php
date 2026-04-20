<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        .customer-block { margin-bottom: 22px; page-break-inside: avoid; }
        .customer-head { font-size: 12px; font-weight: 700; color: #1e3a5f; margin: 0 0 6px; }
        .customer-meta { font-size: 9px; color: #64748b; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 7px; text-align: left; font-size: 8px; }
        th { background: #f1f5f9; font-weight: 700; font-size: 7px; text-transform: uppercase; }
        tr:nth-child(even) { background: #f8fafc; }
        .empty { color: #94a3b8; font-style: italic; padding: 8px 0; }
    </style>
</head>
<body>
    @include('exports.pdf._header', ['title' => $title, 'generatedAt' => $generatedAt])

    @php
        use App\Finance\PaymentStatus;
        use App\Finance\PaymentType;
        use App\Shipments\ShipmentStatus;
    @endphp

    @foreach ($customers as $customer)
        <div class="customer-block">
            <p class="customer-head">{{ $customer->name }}</p>
            <p class="customer-meta">
                {{ __('exports.customers_col_customer_code') }}: {{ $customer->customer_code }}
                · {{ __('exports.customers_col_phone') }}: {{ $customer->phone }}
                · {{ __('exports.customers_col_email') }}: {{ $customer->email ?? '—' }}
                · {{ __('exports.customers_col_document') }}: {{ $customer->document ?? '—' }}
            </p>

            @if ($customer->shipments->isEmpty())
                <p class="empty">{{ __('exports.customers_no_shipments') }}</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('exports.customers_col_tracking') }}</th>
                            <th>{{ __('exports.customers_col_shipment_status') }}</th>
                            <th>{{ __('exports.customers_col_shipment_date') }}</th>
                            <th>{{ __('exports.customers_col_messenger') }}</th>
                            <th>{{ __('exports.customers_col_cost') }}</th>
                            <th>{{ __('exports.customers_col_payment_type') }}</th>
                            <th>{{ __('exports.customers_col_payment_status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->shipments as $shipment)
                            <tr>
                                <td style="font-family: DejaVu Sans Mono, monospace;">{{ $shipment->tracking_number }}</td>
                                <td>{{ ShipmentStatus::label($shipment->status) }}</td>
                                <td>{{ $shipment->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                <td>{{ $shipment->assignedCourier?->name ?? __('shipments.unassigned_courier') }}</td>
                                <td>{{ $shipment->cost !== null ? number_format((float) $shipment->cost, 2, ',', '.') : '—' }}</td>
                                <td>{{ $shipment->payment_type ? PaymentType::label($shipment->payment_type) : '—' }}</td>
                                <td>{{ $shipment->payment_status ? PaymentStatus::label($shipment->payment_status) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
</body>
</html>
