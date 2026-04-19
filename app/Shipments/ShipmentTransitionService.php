<?php

namespace App\Shipments;

use App\Models\Shipment;
use App\Models\ShipmentStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShipmentTransitionService
{
    /**
     * Registra el envío inicial y el primer historial (sin estado previo).
     */
    public function createShipmentWithInitialStatus(
        Shipment $shipment,
        ?User $actor
    ): void {
        DB::transaction(function () use ($shipment, $actor): void {
            $shipment->save();

            ShipmentStatusHistory::create([
                'organization_id' => $shipment->organization_id,
                'shipment_id' => $shipment->id,
                'from_status' => null,
                'to_status' => ShipmentStatus::RECEIVED,
                'notes' => null,
                'changed_by_user_id' => $actor?->id,
            ]);
        });
    }

    /**
     * Cambia estado: actualiza envío y anexa fila de historial (nunca se borra historial).
     */
    public function transitionTo(
        Shipment $shipment,
        string $newStatus,
        ?string $notes,
        ?User $actor
    ): void {
        if ($shipment->status === $newStatus) {
            return;
        }

        if (! in_array($newStatus, ShipmentStatus::all(), true)) {
            throw ValidationException::withMessages([
                'status' => [__('shipments.invalid_status')],
            ]);
        }

        if (! ShipmentTransitionRules::allows($shipment->status, $newStatus)) {
            throw ValidationException::withMessages([
                'status' => [__('shipments.transition_invalid')],
            ]);
        }

        DB::transaction(function () use ($shipment, $newStatus, $notes, $actor): void {
            $from = $shipment->status;

            ShipmentStatusHistory::create([
                'organization_id' => $shipment->organization_id,
                'shipment_id' => $shipment->id,
                'from_status' => $from,
                'to_status' => $newStatus,
                'notes' => $notes,
                'changed_by_user_id' => $actor?->id,
            ]);

            $shipment->update(['status' => $newStatus]);
        });
    }
}
