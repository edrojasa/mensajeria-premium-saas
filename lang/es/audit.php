<?php

return [
    'shipment_created' => 'Envío creado: :tracking',
    'shipment_updated' => 'Envío actualizado: :tracking',
    'shipment_status_changed' => 'Estado: :from → :to',
    'shipment_messenger_assigned' => 'Mensajero asignado (:name) en guía :tracking',
    'customer_created' => 'Cliente creado: :name',
    'customer_updated' => 'Cliente actualizado: :name',
    'user_created' => 'Usuario creado: :name (:email)',
    'user_updated' => 'Usuario actualizado: :name',
    'user_suspended' => 'Cuenta suspendida: :name',
    'user_activated' => 'Cuenta reactivada: :name',
    'customer_deactivated' => 'Cliente desactivado: :name',
    'customer_activated' => 'Cliente reactivado: :name',
    'customer_force_deleted' => 'Cliente eliminado definitivamente: :name',
    'shipment_deactivated' => 'Envío cancelado/archivado: :tracking',
    'shipment_evidence_uploaded' => 'Evidencia registrada en envío :tracking',
    'user_removed_from_org' => 'Usuario retirado de la organización: :name',

    /**
     * Etiquetas legibles para la columna "Acción" en auditoría (clave = valor guardado en BD).
     */
    'action_labels' => [
        'shipment.created' => 'Creación de envío',
        'shipment.updated' => 'Actualización de envío',
        'shipment.status_changed' => 'Cambio de estado de envío',
        'shipment.courier_note' => 'Nota operativa en envío',
        'shipment.messenger_assigned' => 'Asignación de mensajero',
        'shipment.rate_changed' => 'Cambio de tarifa de envío',
        'shipment.cost_changed' => 'Cambio de costo de envío',
        'shipment.payment_changed' => 'Cambio de pago de envío',
        'customer.created' => 'Creación de cliente',
        'customer.updated' => 'Actualización de cliente',
        'user.created' => 'Creación de usuario',
        'user.updated' => 'Actualización de usuario',
        'user.suspended' => 'Suspensión de cuenta',
        'user.activated' => 'Reactivación de cuenta',
        'customer.deactivated' => 'Desactivación de cliente',
        'customer.force_deleted' => 'Eliminación definitiva de cliente',
        'shipment.deactivated' => 'Cancelación / archivo de envío',
        'shipment.evidence_uploaded' => 'Evidencia de envío',
        'user.force_deleted' => 'Eliminación / retiro de usuario',
    ],
];
