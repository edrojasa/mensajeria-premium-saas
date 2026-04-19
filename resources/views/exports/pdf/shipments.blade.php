<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f1f5f9; font-weight: 700; font-size: 9px; text-transform: uppercase; }
        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    @php
        use App\Shipments\ShipmentStatus;
    @endphp
    @include('exports.pdf._header', ['title' => $title, 'generatedAt' => $generatedAt])

    <table>
        <thead>
            <tr>
                <th>{{ __('exports.shipments_col_tracking') }}</th>
                <th>{{ __('exports.shipments_col_customer') }}</th>
                <th>{{ __('exports.shipments_col_recipient') }}</th>
                <th>{{ __('exports.shipments_col_dest_address') }}</th>
                <th>{{ __('exports.shipments_col_dest_city') }}</th>
                <th>{{ __('exports.shipments_col_origin_city') }}</th>
                <th>{{ __('exports.shipments_col_status') }}</th>
                <th>{{ __('exports.shipments_col_messenger') }}</th>
                <th>{{ __('exports.shipments_col_created') }}</th>
                <th>{{ __('exports.shipments_col_delivered') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $s)
                @php
                    $del = $s->delivered_logged_at ?? null;
                @endphp
                <tr>
                    <td style="font-family: monospace;">{{ $s->tracking_number }}</td>
                    <td>{{ $s->customer?->name ?? '—' }}</td>
                    <td>{{ $s->recipient_name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($s->destination_address_line, 48) }}</td>
                    <td>{{ $s->destination_city }}</td>
                    <td>{{ $s->origin_city }}</td>
                    <td>{{ ShipmentStatus::label($s->status) }}</td>
                    <td>{{ $s->assignedCourier?->name ?? __('shipments.unassigned_courier') }}</td>
                    <td>{{ $s->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                    <td>{{ $del ? \Carbon\Carbon::parse($del)->timezone(config('app.timezone'))->format('d/m/Y H:i') : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
