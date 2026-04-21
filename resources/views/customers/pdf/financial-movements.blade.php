@extends('pdf.layout', ['title' => $title, 'generatedAt' => $generatedAt])

@section('content')
    <style>
        .summary { margin-bottom: 12px; border: 1px solid #cbd5e1; padding: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; }
    </style>

    <div class="summary">
        <strong>Cliente:</strong> {{ $customer->name }}<br>
        <strong>Total movimientos:</strong> ${{ number_format($total, 2, ',', '.') }}<br>
        <strong>Envíos pendientes:</strong> {{ $pendingCount }}<br>
        <strong>Envíos pagados:</strong> {{ $paidCount }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Envío</th>
                <th>Valor</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ optional($row['date'])->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                    <td>{{ $row['tracking'] }}</td>
                    <td>${{ number_format((float) $row['value'], 2, ',', '.') }}</td>
                    <td>{{ \App\Finance\PaymentStatus::label($row['status']) }}</td>
                    <td>{{ $row['user'] }}</td>
                    <td>{{ $row['type'] === 'pago' ? __('finance.type_payment') : __('finance.type_charge') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
