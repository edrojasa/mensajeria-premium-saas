<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;
use App\Shipments\ShipmentStatus;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return ! $user->isMessenger();
    }

    public function view(User $user, Shipment $shipment): bool
    {
        if (! $user->belongsToOrganization($shipment->organization_id)) {
            return false;
        }

        if ($user->isMessenger()) {
            return $shipment->assigned_user_id !== null && (int) $shipment->assigned_user_id === (int) $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->canOperateLogistics();
    }

    public function update(User $user, Shipment $shipment): bool
    {
        if ($user->isMessenger()) {
            return false;
        }

        return $user->belongsToOrganization($shipment->organization_id)
            && $user->canOperateLogistics();
    }

    /**
     * Cambiar estado / notas operativas (mensajeros solo en envíos asignados).
     */
    public function updateStatus(User $user, Shipment $shipment): bool
    {
        if (! $user->belongsToOrganization($shipment->organization_id)) {
            return false;
        }

        if ($user->isMessenger()) {
            return $shipment->assigned_user_id !== null
                && (int) $shipment->assigned_user_id === (int) $user->id;
        }

        return $user->canOperateLogistics();
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        return $this->update($user, $shipment);
    }

    /**
     * Archivar envío (estado cancelado + soft delete).
     */
    public function deactivate(User $user, Shipment $shipment): bool
    {
        if (! $user->belongsToOrganization($shipment->organization_id)) {
            return false;
        }

        if ($user->isMessenger()) {
            return false;
        }

        return $user->canOperateLogistics();
    }

    /**
     * Notas / fotos de evidencia en ruta.
     */
    public function addEvidence(User $user, Shipment $shipment): bool
    {
        if (! $user->belongsToOrganization($shipment->organization_id)) {
            return false;
        }

        if ($shipment->trashed()) {
            return false;
        }

        if ($shipment->status === ShipmentStatus::CANCELLED) {
            return false;
        }

        if ($user->isMessenger()) {
            return $shipment->assigned_user_id !== null
                && (int) $shipment->assigned_user_id === (int) $user->id
                && $user->isActiveInCurrentOrganization();
        }

        return $user->canOperateLogistics();
    }

    public function viewReport(User $user, Shipment $shipment): bool
    {
        return $this->view($user, $shipment);
    }

    public function viewGuide(User $user, Shipment $shipment): bool
    {
        return $this->view($user, $shipment);
    }

    public function updatePayment(User $user, Shipment $shipment): bool
    {
        return $user->belongsToOrganization($shipment->organization_id)
            && $user->canAccessFinancialModule();
    }
}
