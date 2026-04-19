<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Shipment $shipment): bool
    {
        return $user->belongsToOrganization($shipment->organization_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Shipment $shipment): bool
    {
        return $user->belongsToOrganization($shipment->organization_id);
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->belongsToOrganization($shipment->organization_id);
    }

    public function viewGuide(User $user, Shipment $shipment): bool
    {
        return $user->belongsToOrganization($shipment->organization_id);
    }
}
