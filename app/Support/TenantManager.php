<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Session\Session;

class TenantManager
{
    public const SESSION_KEY = 'current_organization_id';

    public function __construct(
        private Session $session
    ) {
    }

    public function currentOrganizationId(): ?int
    {
        $id = $this->session->get(self::SESSION_KEY);

        return $id !== null ? (int) $id : null;
    }

    public function currentOrganization(): ?Organization
    {
        $id = $this->currentOrganizationId();

        return $id ? Organization::find($id) : null;
    }

    public function synchronizeSessionOrganization(User $user): void
    {
        $id = $this->currentOrganizationId();

        if ($id !== null && $user->belongsToOrganization($id)) {
            return;
        }

        $first = $user->organizations()->orderBy('organizations.name')->first();

        if ($first !== null) {
            $this->session->put(self::SESSION_KEY, $first->id);

            return;
        }

        $this->session->forget(self::SESSION_KEY);
    }

    public function switchOrganization(User $user, int $organizationId): void
    {
        if (! $user->belongsToOrganization($organizationId)) {
            abort(403, __('No tienes acceso a esta organización.'));
        }

        $this->session->put(self::SESSION_KEY, $organizationId);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }
}
