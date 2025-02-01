<!DOCTYPE html>
<html>
<head>
    <title>Pedido #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pedido #{{ $order->number }}</h1>
        <p>Data: {{ $order->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="details">
        <h3>Detalhes do Cliente</h3>
        <p>Cliente: {{ $order->quote->client->name ?? 'N/A' }}</p>
        <p>Email: {{ $order->quote->client->email ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unit.</th>
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
        <h3>Total: R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</h3>
    </div>
</body>
</html> 