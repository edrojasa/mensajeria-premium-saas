<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        th { background: #f1f5f9; font-weight: 700; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    @include('exports.pdf._header', ['title' => $title, 'generatedAt' => $generatedAt])

    <table>
        <thead>
            <tr>
                <th>{{ __('exports.customers_col_name') }}</th>
                <th>{{ __('exports.customers_col_phone') }}</th>
                <th>{{ __('exports.customers_col_email') }}</th>
                <th>{{ __('exports.customers_col_document') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->email ?? '—' }}</td>
                    <td>{{ $c->document ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
