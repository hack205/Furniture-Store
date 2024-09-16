<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #343a40;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h5 {
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
        }
        .info-section p {
            margin: 0;
        }
        .info-section .row + .row {
            margin-top: 10px;
        }
        .product-list {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .totals {
            font-weight: bold;
            font-size: 1.2rem;
            color: #495057;
        }
        .payments-table th, .payments-table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Orden #{{ $order->number }}</h1>
            <p>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>
        <div class="info-section">
            <h5>Información del Cliente</h5>
            <div class="row">
                <div class="col-md-4"><strong>Nombre:</strong> {{ $order->customer->name }}</div>
                <div class="col-md-4"><strong>Dirección:</strong> {{ $order->customer->address }}</div>
                <div class="col-md-4"><strong>Colonia:</strong> {{ $order->customer->colony }}</div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Ciudad:</strong> {{ $order->customer->city }}</div>
                <div class="col-md-4"><strong>Entre Calles:</strong> {{ $order->customer->street_between_1}} Y {{ $order->customer->street_between_2}}</div>  
            </div>
        </div>
        <div class="info-section">
            <h5>Información de la Orden</h5>
            <div class="row">
                <div class="col-md-4"><strong>Agente:</strong> {{ $order->agent ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Condición de Pago:</strong> {{ $order->payment_conditions }}</div>
            </div>
        </div>

        <div class="product-list">
            <h5>Productos</h5>
            <ul class="list-group">
                @if($order->product)
                    <li class="list-group-item">
                        {{ $order->product }}
                    </li>
                @else
                    <li class="list-group-item">No products available.</li>
                @endif
            </ul>
        </div>

        <div class="row totals">
            @php
                $primerPago = $order->payments->first();

                $adelanto = $primerPago ? $primerPago->amount : 0;
                
                $totalPayments = $order->payments->sum('amount');
                
                $restante = $order->total - $totalPayments;
            @endphp

            <div class="col-md-4"><strong>Total:</strong> ${{ number_format($order->total, 2) }}</div>
            <div class="col-md-4"><strong>Anticipo:</strong> ${{ number_format($adelanto, 2) }}</div>
            <div class="col-md-4"><strong>Saldo Restante:</strong> ${{ number_format($restante, 2) }}</div>
        </div>

        <div class="payments-table">
            <br>
            <h5>Pagos Realizados</h5>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha de Pago</th>
                        <th>Abono</th>
                        <th>Saldo Restante</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPagos = 0;
                    @endphp
                    @foreach($order->payments as $payment)
                        @php
                            $totalPagos += $payment->amount;
                            $restante = $order->total - $totalPagos; 
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>${{ number_format($restante, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
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
