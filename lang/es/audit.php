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

    /**
     * Etiquetas legibles para la columna "Acción" en auditoría (clave = valor guardado en BD).
     */
    'action_labels' => [
        'shipment.created' => 'Creación de envío',
        'shipment.updated' => 'Actualización de envío',
        'shipment.status_changed' => 'Cambio de estado de envío',
        'shipment.courier_note' => 'Nota operativa en envío',
        'shipment.messenger_assigned' => 'Asignación de mensajero',
        'customer.created' => 'Creación de cliente',
        'customer.updated' => 'Actualización de cliente',
        'user.created' => 'Creación de usuario',
        'user.updated' => 'Actualización de usuario',
    ],
];
