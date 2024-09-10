<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            margin: 0; 
            size: 21.59cm 27.94cm; 
        }
        @media print {
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                position: relative;
                width: 100%;
                height: 100%; 
            }
            .field {
                position: absolute;
                font-size: 9pt;
            }
            .no-1 {
                top: 4cm;
                left: 7.2cm;
            }
            .no-2 {
                top: 4cm;
                left: 16.6cm;
            }
            .a-1 {
                top: 4.4cm;
                left: 5.5cm;
            }
            .a-2 {
                top: 4.4cm;
                left: 14.5cm;
            }
            .de-1 {
                top: 4.4cm;
                left: 6.5cm;
            }
            .de-2 {
                top: 4.4cm;
                left: 15.5cm;
            }
            .del-1 {
                top: 4.4cm;
                left: 7.5cm;
            }
            .del-2 {
                top: 4.4cm;
                left: 16.5cm;
            }
            .nombre-1 {
                top: 5.2cm;
                left: 3cm;
            }
            .nombre-2 {
                top: 5.2cm;
                left: 12cm;
            }
            .direccion-1 {
                top: 5.6cm;
                left: 3cm;
            }
            .direccion-2 {
                top: 5.6cm;
                left: 12cm;
            }
            .entre-1 {
                top: 6.2cm;
                left: 3cm;
            }
            .entre-2 {
                top: 6.2cm;
                left: 12cm;
            }
            .colonia-1 {
                top: 6.6cm;
                left: 3cm;
            }
            .colonia-2 {
                top: 6.6cm;
                left: 12cm;
            }
            .ciudad-1 {
                top: 7cm;
                left: 3cm;
            }
            .ciudad-2 {
                top: 7cm;
                left: 12cm;
            }
            .mercancia-1 {
                top: 7.4cm;
                left: 3cm;
            }
            .mercancia-2 {
                top: 7.4cm;
                left: 12cm;
            }
            .total-1 {
                top: 8.3cm;
                left: 7cm;
            }
            .total-2 {
                top: 8.3cm;
                left: 16cm;
            }
            .condiciones-pago-1 {
                top: 8.6cm;
                left: 1cm;
            }
            .condiciones-pago-2 {
                top: 8.6cm;
                left: 10cm;
            }
            .anticipo-1 {
                top: 8.6cm;
                left: 7cm;
            }
            .anticipo-2 {
                top: 8.6cm;
                left: 16cm;
            }
            .saldo-1 {
                top: 9cm;
                left: 7cm;
            }
            .saldo-2 {
                top: 9cm;
                left: 16cm;
            }
        }
    </style>
</head>
<body id="printableArea">

    <div class="field no-1">{{ $order->number }}</div>
    <div class="field no-2">{{ $order->number }}</div>

    <div class="field a-1">{{ $order->created_at->format('d') }}</div>
    <div class="field a-2">{{ $order->created_at->format('d') }}</div>

    <div class="field de-1">{{ $order->created_at->format('m') }}</div>
    <div class="field de-2">{{ $order->created_at->format('m') }}</div>

    <div class="field del-1">{{ $order->created_at->format('Y') }}</div>
    <div class="field del-2">{{ $order->created_at->format('Y') }}</div>

    <div class="field nombre-1">{{ $order->customer->name }}</div>
    <div class="field nombre-2">{{ $order->customer->name }}</div>

    <div class="field direccion-1">{{ $order->customer->address }}</div>
    <div class="field direccion-2">{{ $order->customer->address }}</div>

    <div class="field entre-1">{{ $order->customer->between_streets }}</div>
    <div class="field entre-2">{{ $order->customer->between_streets }}</div>

    <div class="field colonia-1">{{ $order->customer->colony }}</div>
    <div class="field colonia-2">{{ $order->customer->colony }}</div>

    <div class="field ciudad-1">{{ $order->customer->city }}</div>
    <div class="field ciudad-2">{{ $order->customer->city }}</div>

    <div class="field mercancia-1">{{ $order->product }}</div>
    <div class="field mercancia-2">{{ $order->product }}</div>

    <div class="field total-1">{{ number_format($order->total, 2) }}</div>
    <div class="field total-2">{{ number_format($order->total, 2) }}</div>

    <div class="field condiciones-pago-1">{{ $order->payment_conditions }}</div>
    <div class="field condiciones-pago-2">{{ $order->payment_conditions }}</div>

    <div class="field anticipo-1">{{ number_format($order->payments->first()->amount ?? 0, 2) }}</div>
    <div class="field anticipo-2">{{ number_format($order->payments->first()->amount ?? 0, 2) }}</div>

    @php
        $totalPaid = $order->payments->sum('amount');
        $remainingAmount = $order->total - $totalPaid;
    @endphp
    <div class="field saldo-1">{{ number_format($remainingAmount, 2) }}</div>
    <div class="field saldo-2">{{ number_format($remainingAmount, 2) }}</div>

</body>

<script>
    function printAndClose() {
        window.print();
    }
    window.onload = printAndClose;
    window.onafterprint = function() {
        window.close();
    };
</script>
</html>
