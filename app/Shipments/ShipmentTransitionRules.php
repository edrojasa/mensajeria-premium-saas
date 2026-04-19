<?php

namespace App\Shipments;

/**
 * Reglas de negocio para cambios de estado (productivo).
 *
 * Flujo principal: received → in_transit → out_for_delivery → delivered
 * Cualquier estado puede pasar a incidencia.
 * Desde incidencia solo se recupera hacia en tránsito (reproceso).
 */
final class ShipmentTransitionRules
{
    /**
     * ¿Se permite pasar de $from a $to?
     */
    public static function allows(string $from, string $to): bool
    {
        if ($from === $to) {
            return false;
        }

        if ($to === ShipmentStatus::INCIDENT) {
            return true;
        }

        return match ($from) {
            ShipmentStatus::RECEIVED => $to === ShipmentStatus::IN_TRANSIT,
            ShipmentStatus::IN_TRANSIT => $to === ShipmentStatus::OUT_FOR_DELIVERY,
            ShipmentStatus::OUT_FOR_DELIVERY => $to === ShipmentStatus::DELIVERED,
            ShipmentStatus::DELIVERED => false,
            ShipmentStatus::INCIDENT => $to === ShipmentStatus::IN_TRANSIT,
        };
    }

    /**
     * Estados a los que se puede transicionar desde el estado actual.
     *
     * @return list<string>
     */
    public static function allowedTargets(string $from): array
    {
        $targets = [];

        foreach (ShipmentStatus::all() as $candidate) {
            if (self::allows($from, $candidate)) {
                $targets[] = $candidate;
            }
        }

        return $targets;
    }
}
