<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use App\Organizations\OrganizationRole;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageCustomers();
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->canManageCustomers()
            && $user->belongsToOrganization($customer->organization_id);
    }

    public function create(User $user): bool
    {
        return $user->canManageCustomers();
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->view($user, $customer);
    }

    /**
     * Desactivar cliente (is_active = false).
     */
    public function deactivate(User $user, Customer $customer): bool
    {
        return $this->update($user, $customer);
    }

    /**
     * Eliminación física (solo administrador del tenant).
     */
    public function forceDestroy(User $user, Customer $customer): bool
    {
        if (! $user->belongsToOrganization($customer->organization_id)) {
            return false;
        }

        return OrganizationRole::hasFullAccess($user->roleInCurrentOrganization());
    }
}
