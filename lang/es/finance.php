<?php

return [
    'menu_main' => 'Finanzas',
    'menu_reports' => 'Reportes financieros',
    'menu_receivables' => 'Cartera',
    'menu_rates' => 'Tarifas',

    'reports_title' => 'Reportes financieros',
    'reports_subtitle' => 'Resumen del mes y tendencia reciente',
    'receivables_title' => 'Cartera pendiente',
    'receivables_subtitle' => 'Envíos con saldo por cobrar',

    'kpi_billed_month' => 'Facturado (mes)',
    'kpi_paid_month' => 'Pagado (mes)',
    'kpi_pending' => 'Cartera pendiente',
    'kpi_billed_hint' => 'Suma de costos de envíos registrados este mes',
    'kpi_paid_hint' => 'Pagos registrados en el mes',
    'kpi_pending_hint' => 'Saldo pendiente total',

    'chart_title' => 'Facturado vs pagado',
    'chart_subtitle' => 'Últimos 6 meses',
    'chart_billed' => 'Facturado',
    'chart_paid' => 'Pagado',

    'rates_title' => 'Tarifas por tipo de servicio',
    'rates_subtitle' => 'Define precio base y componentes opcionales por kg y km',
    'rate_create' => 'Nueva tarifa',
    'rate_edit' => 'Editar tarifa',
    'rate_saved' => 'Tarifa guardada.',
    'rate_updated' => 'Tarifa actualizada.',
    'rate_deleted' => 'Tarifa eliminada.',
    'confirm_delete_rate' => '¿Eliminar esta tarifa?',
    'delete_short' => 'Eliminar',
    'rate_empty' => 'No hay tarifas. Crea al menos una para calcular costos automáticamente.',

    'field_service_type' => 'Tipo de servicio',
    'field_distance_km' => 'Distancia (km)',
    'field_payment_type' => 'Tipo de pago',
    'field_payment_status' => 'Estado del cobro',
    'field_paid_amount' => 'Monto pagado',
    'field_payment_date' => 'Fecha de pago',
    'field_base_price' => 'Precio base',
    'field_price_per_kg' => 'Precio por kg',
    'field_price_per_km' => 'Precio por km',
    'field_active' => 'Activa',

    'service_types' => [
        'standard' => 'Estándar',
        'express' => 'Express',
        'economy' => 'Económico',
    ],

    'payment_types' => [
        'cash' => 'Contado',
        'credit' => 'Crédito',
    ],

    'payment_statuses' => [
        'pending' => 'Pendiente',
        'paid' => 'Pagado',
    ],

    'section_shipment_finance' => 'Servicio y cobro',
    'cost_calculated' => 'Costo calculado',
    'cost_missing_rate' => 'Sin tarifa activa para este tipo de servicio; define tarifas en el menú correspondiente.',
    'payment_section' => 'Pago del envío',
    'payment_updated' => 'Información de pago actualizada.',
    'record_payment' => 'Registrar pago',

    'col_balance' => 'Saldo',
    'col_days_open' => 'Días abierto',
    'unknown_customer_group' => 'Clientes no registrados',
    'total_general' => 'Total general cartera',
    'total_no_customer' => 'Total sin cliente',
    'receivables_pdf_title' => 'Informe de Cartera',

    'customer_finance_title' => 'Información financiera',
    'customer_finance_billed' => 'Total facturado',
    'customer_finance_paid' => 'Total pagado',
    'customer_finance_balance' => 'Saldo pendiente',
    'customer_financial_shipments_detail' => 'Historial de pagos por envío',

    'dashboard_billed_month' => 'Ingresos facturados (mes)',
    'dashboard_paid_month' => 'Pagos recibidos (mes)',
    'dashboard_pending_balance' => 'Cartera pendiente',
];
