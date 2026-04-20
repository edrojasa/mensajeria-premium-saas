<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('shipments.guide_title') }} — {{ $shipment->tracking_number }}</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        .header { background: linear-gradient(90deg, #1d4ed8, #2563eb); color: #fff; padding: 18px 20px; border-radius: 10px 10px 0 0; }
        .brand { font-size: 10px; opacity: 0.95; margin: 0; }
        .org { font-size: 15px; font-weight: bold; margin: 4px 0 0; }
        .track-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85; margin: 0; }
        .track-val { font-family: DejaVu Sans Mono, monospace; font-size: 18px; margin: 4px 0 0; }
        .card { border: 1px solid #e2e8f0; border-top: none; padding: 18px 20px; border-radius: 0 0 10px 10px; }
        .row { width: 100%; margin-top: 14px; }
        .col { vertical-align: top; width: 50%; padding: 10px; background: #f8fafc; border-radius: 8px; }
        .label { font-size: 9px; text-transform: uppercase; color: #64748b; margin: 0 0 4px; }
        h3 { font-size: 11px; color: #1d4ed8; margin: 0 0 8px; text-transform: uppercase; }
        .status { font-size: 20px; font-weight: bold; margin: 6px 0 4px; }
        .muted { color: #64748b; font-size: 10px; }
        .qr-wrap { text-align: right; }
        .qr { width: 120px; height: 120px; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .footer { font-size: 9px; color: #94a3b8; margin-top: 18px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%" cellspacing="0">
            <tr>
                <td>
                    @if (brand_logo_can_embed_in_pdf())
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(brand_logo_public_path())) }}" alt="" style="max-height: 40px; width: auto; display: block; margin-bottom: 8px;" />
                    @endif
                    <p class="brand">{{ __('brand.name') }}</p>
                    <p class="org">{{ $shipment->organization->name }}</p>
                </td>
                <td style="text-align: right;">
                    <p class="track-label">{{ __('shipments.order_number') }}</p>
                    <p class="track-val">{{ $shipment->tracking_number }}</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="card">
        <table class="meta">
            <tr>
                <td>
                    <p class="label">{{ __('shipments.current_status') }}</p>
                    <p class="status">{{ $shipment->statusLabel() }}</p>
                    <p class="muted">{{ __('shipments.guide_printed_at') }}: {{ $printedAt->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                </td>
                <td class="qr-wrap">
                    <img class="qr" src="{{ $qrDataUri }}" alt="QR"/>
                    <p class="muted" style="margin-top:4px;">{{ __('shipments.guide_qr_label') }}</p>
                </td>
            </tr>
        </table>

        <table class="row" cellspacing="10">
            <tr>
                <td class="col">
                    <h3>{{ __('shipments.sender_section') }}</h3>
                    <p style="margin:0;font-weight:bold;">{{ $shipment->sender_name }}</p>
                    <p style="margin:4px 0 0;color:#475569;">{{ $shipment->sender_phone }}</p>
                    @if ($shipment->sender_email)
                        <p style="margin:2px 0 0;color:#475569;">{{ $shipment->sender_email }}</p>
                    @endif
                    <p class="label" style="margin-top:12px;">{{ __('shipments.origin_section') }}</p>
                    <p style="margin:4px 0 0;">{!! nl2br(e($shipment->origin_address_line)) !!}</p>
                    <p style="margin:6px 0 0;color:#475569;">{{ $shipment->origin_city }}@if ($shipment->origin_region), {{ $shipment->origin_region }}@endif</p>
                </td>
                <td class="col">
                    <h3>{{ __('shipments.recipient_section') }}</h3>
                    <p style="margin:0;font-weight:bold;">{{ $shipment->recipient_name }}</p>
                    <p style="margin:4px 0 0;color:#475569;">{{ $shipment->recipient_phone }}</p>
                    @if ($shipment->recipient_email)
                        <p style="margin:2px 0 0;color:#475569;">{{ $shipment->recipient_email }}</p>
                    @endif
                    <p class="label" style="margin-top:12px;">{{ __('shipments.destination_section') }}</p>
                    <p style="margin:4px 0 0;">{!! nl2br(e($shipment->destination_address_line)) !!}</p>
                    <p style="margin:6px 0 0;color:#475569;">{{ $shipment->destination_city }}@if ($shipment->destination_region), {{ $shipment->destination_region }}@endif</p>
                </td>
            </tr>
        </table>

        <p class="footer">{{ __('tracking.public_disclaimer') }}</p>
    </div>
</body>
</html>
