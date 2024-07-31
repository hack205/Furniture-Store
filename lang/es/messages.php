<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de mensajes
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de mensajes se usan durante la sessión para varios
    | mensajes que necesitamos mostrar al usuario. Eres libre de modificar
    | estas líneas de lenguaje según los requisitos de tu aplicación.
    |
    */

    'shop' => 'Comercio',

    // Navigation

    'agents' => 'Agentes',
    'orders' => 'Órdenes',
    'payments' => 'Pagos',
    'customers' => 'Clientes',

    // Dashboard
    'dashboard' => [
        'increase' => 'aumento',
        'total_orders' => 'Total de órdenes',
        'total_customers' => 'Total Clientes',
        'total_revenue' => 'Ingresos Totales',
        'new_customers_last_mount' => 'Nuevos Clientes (Último mes)',
        'avg_renevue_per_customer' => 'Ingresos promedio por cliente',
        'avg_orders_per_customer' => 'Órdenes promedio por cliente',
    ],


    // Customers
    'customer' => [
        'customer'  => 'cliente',
        'name'      => 'Nombre',
        'phone'     => 'Teléfono',
        'city'      => 'Ciudad',
        'colony'    => 'Colonia',
        'address'   => 'Dirección'
    ],

    // Agents
    'agent' => [
        'agent'     => 'agente',
        'name'      => 'Nombre',
        'phone'     => 'Teléfono',
        'created_at' => 'Creado'
    ],


    // Orders
    'order' => [
        'order'     => 'órden',
        'total'     => 'Total',
        'number'    => 'Número',
        'customer'  => 'Cliente',
        'agent'     => 'Agente',
        'status'    => 'Estado',
        'notes'     => 'Notas',

        'product'   => 'Producto',
        'qty'       => 'Cantidad',
        'unit_price' => 'Precio unitario',

        'order_items' => 'Lista de artículos',
        'order_details' => 'Detalle de órden',


        'created_at' => 'Creado',
        'update_at'  => 'Última actualización',
        'archived_at'=> 'Archivado',

        // Status Enum
        'pending'   => 'Pendiente',
        'processing'=> 'Procesando',
        'completed' => 'Completado',
        'declined'  => 'Cancelado',

        'open_order' => 'Abrir',

        'is_settled' => 'Liquidado',
        'amount_due' => 'Adeudo'
    ],

    // Payments
    'payment' => [
        'payment' => 'Pago',
        'reference' => 'Referencia',
        'amount' => 'Monto',
        'method' => 'Método de pago',
        'created_at' => 'Creado',

        // Payment Types Enum
        'efectivo'   => 'Efectivo',
        'transferencia'=> 'Transferencia',
    ]
];
