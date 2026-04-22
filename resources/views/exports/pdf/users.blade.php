<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-weight: 700; font-size: 9px; text-transform: uppercase; }
        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    @include('exports.pdf._header', ['title' => $title, 'generatedAt' => $generatedAt])

    @php
        use App\Enums\UserAccountStatus;
        use App\Organizations\OrganizationRole;
    @endphp

    <table>
        <thead>
            <tr>
                <th>{{ __('exports.users_col_name') }}</th>
                <th>{{ __('exports.users_col_email') }}</th>
                <th>{{ __('exports.users_col_phone') }}</th>
                <th>{{ __('exports.users_col_role') }}</th>
                <th>{{ __('exports.users_col_active') }}</th>
                <th>{{ __('exports.users_col_account') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $user)
                @php
                    $org = $user->organizations->firstWhere('id', $organizationId);
                    $pivot = $org?->pivot;
                    $acct = $user->status ?? UserAccountStatus::ACTIVE;
                @endphp
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>{{ $pivot ? OrganizationRole::label((string) $pivot->role) : '—' }}</td>
                    <td>{{ $pivot && $pivot->is_active ? __('users.active_in_org') : __('users.inactive_in_org') }}</td>
                    <td>{{ UserAccountStatus::label($acct) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
