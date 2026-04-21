@extends('pdf.layout', ['title' => $title, 'generatedAt' => $generatedAt])

@section('content')
    <style>
        .kpis { margin-bottom: 12px; border: 1px solid #cbd5e1; padding: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; }
    </style>

    <div class="kpis">
        <strong>Total ingresos:</strong> ${{ number_format($globalMetrics['total_income'], 2, ',', '.') }}<br>
        <strong>Total pendiente cobro:</strong> ${{ number_format($globalMetrics['total_pending_collection'], 2, ',', '.') }}<br>
        <strong>Total envíos:</strong> {{ $globalMetrics['total_shipments'] }}
    </div>

    <h3>Resumen por mensajero</h3>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Total envíos</th>
                <th>Entregados</th>
                <th>Pendientes</th>
                <th>Ingresos</th>
                <th>Pendiente cobro</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summaryRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['active'] ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $row['total_shipments'] }}</td>
                    <td>{{ $row['delivered'] }}</td>
                    <td>{{ $row['pending'] }}</td>
                    <td>${{ number_format($row['income_generated'], 2, ',', '.') }}</td>
                    <td>${{ number_format($row['pending_collection'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 12px;">Historial</h3>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Guía</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Valor</th>
                <th>Estado pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($history as $shipment)
                <tr>
                    <td>{{ $shipment->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                    <td>{{ $shipment->tracking_number }}</td>
                    <td>{{ $shipment->customer?->name ?? __('finance.unknown_customer_group') }}</td>
                    <td>{{ \App\Shipments\ShipmentStatus::label($shipment->status) }}</td>
                    <td>${{ number_format((float) ($shipment->cost ?? 0), 2, ',', '.') }}</td>
                    <td>{{ \App\Finance\PaymentStatus::label($shipment->payment_status ?? \App\Finance\PaymentStatus::PENDING) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
