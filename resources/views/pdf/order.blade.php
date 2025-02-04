<!DOCTYPE html>
<html>
<head>
    <title>Pedido #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #444;
        }
        .logo {
            margin-bottom: 10px;
        }
        .details {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>{{ auth()->user()->tenant->company_name ?? config('app.name') }}</h1>
        </div>
        <h2>Pedido #{{ $order->number }}</h2>
        <p>Data: {{ $order->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="details">
        <h3>Detalhes do Cliente</h3>
        <p><strong>Nome:</strong> {{ $order->quote->client->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $order->quote->client->email ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->quote->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td>{{ $item->quantity ?? 0 }}</td>
                <td>R$ {{ number_format($item->unit_price ?? 0, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($item->total_price ?? 0, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}
    </div>
</body>
</html>
