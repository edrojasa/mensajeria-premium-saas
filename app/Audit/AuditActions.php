<?php

namespace App\Audit;

final class AuditActions
{
    public const SHIPMENT_CREATED = 'shipment.created';

    public const SHIPMENT_UPDATED = 'shipment.updated';

    public const SHIPMENT_STATUS_CHANGED = 'shipment.status_changed';

    public const SHIPMENT_DEACTIVATED = 'shipment.deactivated';

    public const SHIPMENT_EVIDENCE_UPLOADED = 'shipment.evidence_uploaded';

    public const SHIPMENT_COURIER_NOTE = 'shipment.courier_note';

    public const SHIPMENT_MESSENGER_ASSIGNED = 'shipment.messenger_assigned';

    public const CUSTOMER_CREATED = 'customer.created';

    public const CUSTOMER_UPDATED = 'customer.updated';

    public const CUSTOMER_DEACTIVATED = 'customer.deactivated';

    public const CUSTOMER_FORCE_DELETED = 'customer.force_deleted';

    public const USER_CREATED = 'user.created';

    public const USER_UPDATED = 'user.updated';

    public const USER_DEACTIVATED = 'user.deactivated';

    public const USER_FORCE_DELETED = 'user.force_deleted';
}
