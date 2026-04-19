<?php

return [
    'menu' => 'Envíos',

    'title' => 'Envíos',
    'subtitle_index' => 'Gestiona guías y estados desde un solo lugar.',
    'module_heading' => 'Centro de envíos',
    'index_table_hint' => 'Filas ordenadas por fecha de creación. La guía es única por organización.',
    'actions_column' => 'Acción',
    'subtitle_create' => 'Registrar nuevo envío',
    'subtitle_show' => 'Detalle del envío',

    'create_action' => 'Crear envío',
    'create_button' => 'Guardar envío',

    'empty' => 'No hay envíos registrados.',

    'tracking_number' => 'Número de seguimiento',
    'order_number' => 'Número de guía',
    'current_status' => 'Estado actual',
    'reference_internal' => 'Referencia interna',
    'notes_internal' => 'Notas internas',
    'weight_kg' => 'Peso (kg)',
    'declared_value' => 'Valor declarado',

    'sender_section' => 'Remitente',
    'recipient_section' => 'Destinatario',
    'origin_section' => 'Origen',
    'destination_section' => 'Destino',
    'destination_city' => 'Ciudad destino',

    'sender_name' => 'Nombre remitente',
    'sender_phone' => 'Teléfono remitente',
    'sender_email' => 'Correo remitente',

    'recipient_name' => 'Nombre destinatario',
    'recipient_phone' => 'Teléfono destinatario',
    'recipient_email' => 'Correo destinatario',

    'address_line' => 'Dirección',
    'city' => 'Ciudad',
    'region' => 'Departamento / estado',
    'department' => 'Departamento',
    'select_department' => 'Seleccione departamento',
    'select_city' => 'Seleccione ciudad',
    'departments_missing_hint' => 'No hay departamentos cargados. Ejecute la migración y «php artisan db:seed --class=ColombiaGeoSeeder».',
    'postal_code' => 'Código postal',

    'history_section' => 'Historial de estados',
    'history_empty' => 'Sin movimientos registrados.',
    'history_from' => 'Estado anterior',
    'history_to' => 'Nuevo estado',
    'history_notes' => 'Notas',
    'history_at' => 'Fecha',
    'history_by' => 'Usuario',

    'change_status_section' => 'Actualizar estado',
    'new_status' => 'Nuevo estado',
    'status_notes' => 'Notas del cambio',
    'status_notes_help' => 'Opcional. Obligatorio recomendado para incidencias.',
    'update_status_button' => 'Registrar cambio',

    'created_success' => 'Envío registrado correctamente.',
    'status_updated' => 'Estado actualizado correctamente.',
    'invalid_status' => 'El estado indicado no es válido.',
    'transition_invalid' => 'No está permitido pasar a ese estado desde el estado actual.',
    'no_status_changes' => 'No hay cambios de estado disponibles para este envío.',
    'public_tracking_link' => 'Enlace público de seguimiento',
    'public_tracking_copy_hint' => 'Comparte este enlace con el cliente (sin datos internos).',

    'created_at_column' => 'Fecha de registro',
    'view_detail' => 'Ver detalle',

    'section_optional' => 'Opcional',
    'additional_section' => 'Detalles adicionales',
    'cancel' => 'Cancelar',
    'back_to_list' => '← Volver al listado',

    'status' => [
        'received' => 'Recibido',
        'in_transit' => 'En tránsito',
        'out_for_delivery' => 'En reparto',
        'delivered' => 'Entregado',
        'incident' => 'Incidencia',
    ],

    'show_summary_title' => 'Resumen del envío',
    'timeline_title' => 'Seguimiento del envío',
    'timeline_progress_hint' => 'Avance estimado según el estado operativo actual.',
    'timeline_progress_label' => 'Avance operativo',
    'timeline_incident_banner' => 'Este envío tiene una incidencia registrada. Revise el historial para más detalle.',
    'timeline_done' => 'Completado',
    'timeline_active' => 'En curso',
    'timeline_pending' => 'Pendiente',
    'timeline_incident' => 'Incidencia',

    'guide_section' => 'Guía de envío',
    'guide_title' => 'Guía de envío',
    'guide_view' => 'Ver guía',
    'guide_print' => 'Imprimir',
    'guide_pdf' => 'Descargar PDF',
    'guide_hint' => 'Guía lista para imprimir o compartir con el cliente.',
    'guide_brand_line' => 'Plataforma',
    'guide_party_label' => 'Empresa operadora',
    'guide_qr_label' => 'Seguimiento en línea',
    'guide_printed_at' => 'Documento generado',
];
