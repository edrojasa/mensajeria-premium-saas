<?php

namespace App\Exports;

use App\Enums\UserAccountStatus as AccountStatus;
use App\Organizations\OrganizationRole;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizationUsersExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private Builder $query, private int $organizationId)
    {
    }

    public function query(): Builder
    {
        return $this->query->clone();
    }

    public function headings(): array
    {
        return [
            __('exports.users_col_name'),
            __('exports.users_col_email'),
            __('exports.users_col_phone'),
            __('exports.users_col_role'),
            __('exports.users_col_active'),
            __('exports.users_col_account'),
        ];
    }

    /**
     * @param  \App\Models\User  $user
     * @return array<int, string>
     */
    public function map($user): array
    {
        $org = $user->organizations->firstWhere('id', $this->organizationId);
        $pivot = $org?->pivot;

        return [
            $user->name,
            $user->email,
            $user->phone ?? '—',
            $pivot ? OrganizationRole::label((string) $pivot->role) : '—',
            $pivot && $pivot->is_active ? __('users.active_in_org') : __('users.inactive_in_org'),
            AccountStatus::label($user->status),
        ];
    }
}
