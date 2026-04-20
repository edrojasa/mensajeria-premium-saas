<?php

namespace App\Policies;

use App\Models\ServiceRate;
use App\Models\User;

class ServiceRatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessFinancialModule();
    }

    public function view(User $user, ServiceRate $rate): bool
    {
        return $user->canAccessFinancialModule()
            && $user->belongsToOrganization($rate->organization_id);
    }

    public function create(User $user): bool
    {
        return $user->canAccessFinancialModule();
    }

    public function update(User $user, ServiceRate $rate): bool
    {
        return $user->canAccessFinancialModule()
            && $user->belongsToOrganization($rate->organization_id);
    }

    public function delete(User $user, ServiceRate $rate): bool
    {
        return $this->update($user, $rate);
    }
}
