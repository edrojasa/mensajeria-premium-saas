<?php

namespace Tests\Concerns;

use App\Models\User;
use App\Support\TenantManager;

trait InteractsWithTenant
{
    protected function authenticateWithTenant(?User $user = null): User
    {
        $user ??= User::factory()->withOrganization()->create();

        $organization = $user->organizations()->first();

        $this->actingAs($user);
        session([TenantManager::SESSION_KEY => $organization->id]);

        return $user;
    }
}
