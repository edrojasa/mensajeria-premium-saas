<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ __('shipments.report_pdf_title') }} {{ $shipment->tracking_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 13px; margin-top: 14px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; font-size: 10px; text-transform: uppercase; }
        .muted { color: #555; font-size: 10px; }
        img.ev { max-width: 300px; height: auto; margin-top: 6px; }
    </style>
</head>
<body>
    <h1>{{ __('shipments.report_pdf_title') }}</h1>
    <p class="muted">{{ __('shipments.report_generated_at') }} {{ $printedAt->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>

    <h2>{{ __('shipments.show_summary_title') }}</h2>
    <table>
        <tr><th>{{ __('shipments.order_number') }}</th><td>{{ $shipment->tracking_number }}</td></tr>
        <tr><th>{{ __('shipments.current_status') }}</th><td>{{ \App\Shipments\ShipmentStatus::label($shipment->status) }}</td></tr>
        @if ($shipment->customer)
            <tr><th>{{ __('shipments.customer_linked') }}</th><td>{{ $shipment->customer->name }} ({{ $shipment->customer->customer_code }})</td></tr>
        @endif
        <tr><th>{{ __('shipments.recipient_section') }}</th><td>{{ $shipment->recipient_name }} — {{ $shipment->recipient_phone }}</td></tr>
        <tr><th>{{ __('shipments.origin_section') }}</th><td>{{ $shipment->origin_address_line }} — {{ $shipment->origin_city }}</td></tr>
        <tr><th>{{ __('shipments.destination_section') }}</th><td>{{ $shipment->destination_address_line }} — {{ $shipment->destination_city }}</td></tr>
    </table>

    <h2>{{ __('shipments.history_section') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('shipments.history_at') }}</th>
                <th>{{ __('shipments.history_from') }}</th>
                <th>{{ __('shipments.history_to') }}</th>
                <th>{{ __('shipments.history_notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statusRows as $entry)
                <tr>
                    <td>{{ $entry->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                    <td>{{ $entry->fromStatusLabel() ?? '—' }}</td>
                    <td>{{ $entry->toStatusLabel() }}</td>
                    <td>{{ $entry->notes ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>{{ __('shipments.evidence_section_title') }}</h2>
    @if (! $gdEnabled)
        <p class="muted">{{ __('shipments.pdf_gd_missing_note') }}</p>
    @endif
    @forelse ($evidenceRows as $evidence)
        <div style="margin-bottom: 12px; page-break-inside: avoid;">
            <p class="muted">{{ $evidence->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                — {{ $evidence->author?->name ?? '—' }}</p>
            @if ($evidence->note)
                <p>{{ $evidence->note }}</p>
            @endif
            @if (!empty($evidenceImages[$evidence->id]['data']))
                <img class="ev" src="data:{{ $evidenceImages[$evidence->id]['mime'] }};base64,{{ $evidenceImages[$evidence->id]['data'] }}" alt="">
            @endif
        </div>
    @empty
        <p class="muted">{{ __('shipments.evidence_empty_pdf') }}</p>
    @endforelse
</body>
</html>
