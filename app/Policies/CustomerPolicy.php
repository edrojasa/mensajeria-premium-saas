<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

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
}
