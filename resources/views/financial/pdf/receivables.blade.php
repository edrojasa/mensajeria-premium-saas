@extends('pdf.layout', ['title' => $title, 'generatedAt' => $generatedAt])

@section('content')
    <style>
        .kpi { border: 1px solid #cbd5e1; padding: 8px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; }
    </style>

    <div class="kpi">
        <strong>{{ __('finance.total_general') }}:</strong> ${{ number_format($totalGeneral, 2, ',', '.') }}<br>
        <strong>{{ __('finance.total_no_customer') }}:</strong> ${{ number_format($totalNoCustomer, 2, ',', '.') }}
    </div>

    @foreach ($groups as $group)
        <h3 style="margin-top: 12px;">{{ $group['customer_name'] }} - ${{ number_format($group['total_due'], 2, ',', '.') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('shipments.order_number') }}</th>
                    <th>{{ __('finance.col_balance') }}</th>
                    <th>{{ __('finance.field_payment_status') }}</th>
                    <th>{{ __('exports.shipments_col_created') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group['rows'] as $shipment)
                    <tr>
                        <td>{{ $shipment->tracking_number }}</td>
                        <td>${{ number_format($shipment->balanceDue(), 2, ',', '.') }}</td>
                        <td>{{ \App\Finance\PaymentStatus::label($shipment->payment_status ?? \App\Finance\PaymentStatus::PENDING) }}</td>
                        <td>{{ $shipment->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
