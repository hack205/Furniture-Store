<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$order->customer->name}} | {{$order->number}}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            font-size: 10px;
            border: 1px solid black;
        }

        .header-order {
            padding-right: 20px;
            text-align: right;
        }
        .header-date {
            text-align: right;
        }
    </style>
</head>
<body>
<table id="dataTable">
    <thead>
    <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <tr>
        <th class="header-order">{{$order->number}}</th>
        <th class="header-order">{{$order->number}}</th>
    </tr>
    <tr>
        <th class="header-date">{{ $order->created_at->translatedFormat('d') }}&emsp;&emsp;{{ $order->created_at->translatedFormat('F') }}&emsp;&emsp;{{ $order->created_at->translatedFormat('Y') }}</th>
        <th class="header-date">{{ $order->created_at->translatedFormat('d') }}&emsp;&emsp;{{ $order->created_at->translatedFormat('F') }}&emsp;&emsp;{{ $order->created_at->translatedFormat('Y') }}</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <td>{{$order->customer->name}}</td>
        <td>{{$order->customer->name}}</td>
    </tr>
    <tr>
        <td>{{$order->customer->address}}</td>
        <td>{{$order->customer->address}}</td>
    </tr>
    <tr>
        <td>{{$order->customer->colony}}</td>
        <td>{{$order->customer->colony}}</td>
    </tr>
    <tr>
        <td>{{$order->customer->city}}</td>
        <td>{{$order->customer->city}}</td>
    </tr>
    <tr>
        <td>
            @foreach ($order->items as $item)
                {{ $item->qty }} - {{ $item->name }}
            @endforeach
        </td>

        <td>
            @foreach ($order->items as $item)
                {{ $item->qty }} - {{ $item->name }}
            @endforeach
        </td>
    </tr>
    <tr>
        <td>1</td>
        <td>2</td>
    </tr>
    <tr>
        <td>3</td>
        <td>4</td>
    </tr>
    <tr>
        <td>5</td>
        <td>6</td>
    </tr>
    <tr>
        <td>7</td>
        <td>8</td>
    </tr>
    <tr>
        <td>9</td>
        <td>10</td>
    </tr>
    <tr>
        <td>11</td>
        <td>12</td>
    </tr>
    <tr>
        <td>13</td>
        <td>14</td>
    </tr>

    <tr>
        <td>15</td>
        <td>16</td>
    </tr>
    <tr>
        <td>17</td>
        <td>18</td>
    </tr>
    <tr>
        <td>19</td>
        <td>20</td>
    </tr>
    <tr>
        <td>21</td>
        <td>22</td>
    </tr>
    <tr>
        <td>22</td>
        <td>23</td>
    </tr>
    <tr>
        <td>24</td>
        <td>25</td>
    </tr>

    </tbody>
</table>

<script>
    function printTable() {
        var printContents = document.getElementById('dataTable').outerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
    printTable()
</script>
</body>
</html>
