<?php

namespace App\Actions\Auth;

use App\Models\Organization;
use App\Models\User;
use App\Organizations\OrganizationRole;
use App\Support\DefaultOrganization;

/**
 * Asocia el usuario a la organización por defecto si no pertenece a ninguna.
 * Rol operativo por defecto (no administrador global); el admin inicial va en el seeder.
 */
class EnsureDefaultOrganizationMembership
{
    public function __invoke(User $user): void
    {
        if ($user->organizations()->exists()) {
            return;
        }

        $organization = Organization::query()->firstOrCreate(
            ['slug' => DefaultOrganization::SLUG],
            ['name' => DefaultOrganization::NAME],
        );

        $user->organizations()->syncWithoutDetaching([
            $organization->id => [
                'role' => OrganizationRole::OPERADOR,
                'is_active' => true,
            ],
        ]);
    }
}
