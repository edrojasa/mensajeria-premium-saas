<?php

namespace App\Support;

use App\Models\User;
use App\Organizations\OrganizationRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class OrganizationUsersListing
{
    public static function messengersQuery(Request $request, int $organizationId): Builder
    {
        $query = array_merge($request->query->all(), ['role' => OrganizationRole::MENSAJERO]);

        return self::filteredQuery($request->duplicate($query), $organizationId);
    }

    public static function filteredQuery(Request $request, int $organizationId): Builder
    {
        return User::query()
            ->whereHas('organizations', function ($q) use ($organizationId, $request): void {
                $q->where('organizations.id', $organizationId);
                if ($request->filled('role')) {
                    $q->where('organization_user.role', $request->query('role'));
                }
            })
            ->with(['organizations' => fn ($q) => $q->where('organizations.id', $organizationId)])
            ->orderBy('name');
    }
}
