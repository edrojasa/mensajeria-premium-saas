<?php

namespace App\Audit;

final class AuditActions
{
    public const SHIPMENT_CREATED = 'shipment.created';

    public const SHIPMENT_UPDATED = 'shipment.updated';

    public const SHIPMENT_STATUS_CHANGED = 'shipment.status_changed';

    public const SHIPMENT_COURIER_NOTE = 'shipment.courier_note';

    public const SHIPMENT_MESSENGER_ASSIGNED = 'shipment.messenger_assigned';

    public const CUSTOMER_CREATED = 'customer.created';

    public const CUSTOMER_UPDATED = 'customer.updated';

    public const USER_CREATED = 'user.created';

    public const USER_UPDATED = 'user.updated';
}
