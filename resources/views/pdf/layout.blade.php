<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        .header { background: linear-gradient(90deg, #1d4ed8, #1e3a8a); color: #fff; padding: 14px 18px; width: 100%; }
        .header-table { width: 100%; border-collapse: collapse; }
        .content { margin-top: 12px; }
        .muted { color: #64748b; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 52px; vertical-align: middle;">
                    @if (brand_logo_can_embed_in_pdf())
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(brand_logo_public_path())) }}" alt="{{ config('app.name') }}" style="max-height: 38px; width: auto;" />
                    @endif
                </td>
                <td style="vertical-align: middle;">
                    <div style="font-size: 16px; font-weight: 700;">Mensajería Premium</div>
                    <div style="font-size: 11px;">Plataforma</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="font-size: 14px; font-weight: 700;">{{ $title ?? '' }}</div>
                    @isset($generatedAt)
                        <div style="font-size: 9px;">{{ __('exports.generated_at') }}: {{ $generatedAt->format('d/m/Y H:i') }}</div>
                    @endisset
                </td>
            </tr>
        </table>
    </div>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>
