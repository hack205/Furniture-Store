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
        }
    </style>
    
</head>
<body id="printableArea">
@php
    $anticipo = number_format($order->payments->first()->amount ?? 0, 2);
    $totalPaid = $order->payments->sum('amount');
    $remainingAmount = number_format($order->total - $totalPaid, 2);
@endphp
@foreach($canvasData as $data)
    @php
        $content = '';
        switch ($data['content']) {
            case 'No copia':
            case 'No original':
                $content = $order->number;
                break;
            case 'A copia':
            case 'A original':
                $content = $order->created_at->format('d');
                break;
            case 'De copia':
            case 'De original':
                $content = $order->created_at->format('m');
                break;
            case 'Del copia':
            case 'Del original':
                $content = $order->created_at->format('Y');
                break;
            case 'Nombre copia':
            case 'Nombre original':
                $content = $order->customer->name;
                break;
            case 'Dirección copia':
            case 'Dirección original':
                $content = $order->customer->address;
                break;
            case 'Entre copia':
            case 'Entre original':
                $content = $order->customer->street_between_1;
                break;
            case 'Y copia':
            case 'Y original':
                $content = $order->customer->street_between_2;
                break;
            case 'Colonia copia':
            case 'Colonia original':
                $content = $order->customer->colony;
                break;
            case 'Ciudad copia':
            case 'Ciudad original':
                $content = $order->customer->city;
                break;
            case 'Mercancia copia':
            case 'Mercancia original':
                $content = $order->product;
                break;
            case 'Total copia':
            case 'Total original':
                $content = number_format($order->total, 2);
                break;
            case 'Condiciones de pago copia':
            case 'Condiciones de pago original':
                $content = $order->payment_conditions;
                break;
            case 'Anticipo original':
            case 'Anticipo copia':
                $content = $anticipo;
                break;
            case 'Saldo original':
            case 'Saldo copia':
                $content = $remainingAmount;
                break;
        
        }
    @endphp
        <div class="field" style="top: {{ $data['y'] }}px; left: {{ $data['x'] }}px; font-size: {{ $data['fontSize'] }}px; color: {{ $data['color'] }}; font-family: {{ $data['fontFamily'] }};">
            {{ $content }}
        </div>
    @endforeach

    <script>
        function printAndClose() {
            window.print();
        }
        window.onload = printAndClose;
        window.onafterprint = function() {
            window.close();
        };
    </script>

</body>
</html>
