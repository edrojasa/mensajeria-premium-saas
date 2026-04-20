<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #f1f5f9; font-weight: 700; font-size: 8px; text-transform: uppercase; }
        tr:nth-child(even) { background: #f8fafc; }
        .desc { max-width: 260px; word-break: break-word; }
    </style>
</head>
<body>
    @include('exports.pdf._header', ['title' => $title, 'generatedAt' => $generatedAt])

    <table>
        <thead>
            <tr>
                <th>{{ __('exports.logs_col_date') }}</th>
                <th>{{ __('exports.logs_col_user') }}</th>
                <th>{{ __('exports.logs_col_action') }}</th>
                <th>{{ __('exports.logs_col_description') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $log)
                <tr>
                    <td>{{ $log->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user?->name ?? '—' }}</td>
                    <td>{{ \App\Support\AuditActionPresenter::label($log->action) }}</td>
                    <td class="desc">{{ $log->description ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">{{ __('logs.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
