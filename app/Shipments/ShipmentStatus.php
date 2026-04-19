<?php

namespace App\Shipments;

/**
 * Estados internos (clave estable en base de datos).
 * Las etiquetas visibles están en lang/es/shipments.php y claves status.*
 */
final class ShipmentStatus
{
    public const RECEIVED = 'received';

    public const IN_TRANSIT = 'in_transit';

    public const OUT_FOR_DELIVERY = 'out_for_delivery';

    public const DELIVERED = 'delivered';

    public const INCIDENT = 'incident';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::RECEIVED,
            self::IN_TRANSIT,
            self::OUT_FOR_DELIVERY,
            self::DELIVERED,
            self::INCIDENT,
        ];
    }

    /**
     * Estados permitidos como punto de partida al crear un envío.
     *
     * @return list<string>
     */
    public static function initialAllowed(): array
    {
        return [self::RECEIVED];
    }

    public static function label(string $status): string
    {
        return __("shipments.status.{$status}");
    }
}
